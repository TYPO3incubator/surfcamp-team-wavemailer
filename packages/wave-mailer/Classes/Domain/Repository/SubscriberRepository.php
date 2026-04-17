<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Domain\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Repository;

class SubscriberRepository extends Repository
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
        parent::__construct();
    }

    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param array<int> $groupUids
     * @param array<int|string> $excludeSubscriberUids
     * @return array<int, array<string, mixed>>
     * @throws Exception
     */
    public function findActiveByGroups(array $groupUids, array $excludeSubscriberUids = []): array
    {
        if (empty($groupUids)) {
            return [];
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_wavemailer_domain_model_subscriber');
        $queryBuilder
            ->select('s.*')
            ->from('tx_wavemailer_domain_model_subscriber', 's')
            ->join(
                's',
                'tx_wavemailer_domain_model_subscriptiongroup_subscriber_mm',
                'mm',
                $queryBuilder->expr()->eq('mm.uid_foreign', $queryBuilder->quoteIdentifier('s.uid'))
            )
            ->where(
                $queryBuilder->expr()->eq('s.double_opt_in', 1),
                $queryBuilder->expr()->eq('s.hidden', 0),
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->isNull('s.cancellation_date'),
                    $queryBuilder->expr()->eq('s.cancellation_date', 0)
                ),
                $queryBuilder->expr()->in(
                    'mm.uid_local',
                    $queryBuilder->createNamedParameter($groupUids, ArrayParameterType::INTEGER)
                )
            )
            ->groupBy('s.uid');

        if (!empty($excludeSubscriberUids)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->notIn(
                    's.uid',
                    $queryBuilder->createNamedParameter($excludeSubscriberUids, ArrayParameterType::INTEGER)
                )
            );
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param int $subscriberUid
     * @return string|null
     * @throws Exception
     */
    public function findEmailByUid(int $subscriberUid): ?string
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_wavemailer_domain_model_subscriber');
        $result = $queryBuilder
            ->select('email')
            ->from('tx_wavemailer_domain_model_subscriber')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($subscriberUid, ParameterType::INTEGER))
            )
            ->executeQuery()
            ->fetchAssociative();

        return $result['email'] ?? null;
    }

    /**
     * @param array<int> $groupUids
     * @param array<int|string> $excludeSubscriberUids
     * @throws Exception
     */
    public function countActiveByGroups(array $groupUids, array $excludeSubscriberUids = []): int
    {
        if (empty($groupUids)) {
            return 0;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_wavemailer_domain_model_subscriber');
        $queryBuilder
            ->selectLiteral('COUNT(DISTINCT s.uid)')
            ->from('tx_wavemailer_domain_model_subscriber', 's')
            ->join(
                's',
                'tx_wavemailer_domain_model_subscriptiongroup_subscriber_mm',
                'mm',
                $queryBuilder->expr()->eq('mm.uid_foreign', $queryBuilder->quoteIdentifier('s.uid'))
            )
            ->where(
                $queryBuilder->expr()->eq('s.double_opt_in', 1),
                $queryBuilder->expr()->eq('s.hidden', 0),
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->isNull('s.cancellation_date'),
                    $queryBuilder->expr()->eq('s.cancellation_date', 0)
                ),
                $queryBuilder->expr()->in(
                    'mm.uid_local',
                    $queryBuilder->createNamedParameter($groupUids, ArrayParameterType::INTEGER)
                )
            );

        if (!empty($excludeSubscriberUids)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->notIn(
                    's.uid',
                    $queryBuilder->createNamedParameter($excludeSubscriberUids, ArrayParameterType::INTEGER)
                )
            );
        }

        return (int)$queryBuilder->executeQuery()->fetchOne();
    }

    /**
     * @throws InvalidQueryException
     */
    public function findCancelled(int $days): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $threshold = (new \DateTime())->modify("-{$days} days");
        $query->matching(
            $query->logicalAnd(
                $query->greaterThan('cancellationDate', 0),
                $query->lessThanOrEqual('cancellationDate', $threshold),
                $query->equals('hidden', true),
            )
        );
        return $query->execute();
    }
}
