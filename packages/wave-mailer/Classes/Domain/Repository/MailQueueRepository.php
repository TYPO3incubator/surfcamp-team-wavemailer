<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Domain\Repository;

use Beffp\WaveMailer\Domain\Model\MailQueue;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Repository;

class MailQueueRepository extends Repository
{
    private const TABLE_NAME = 'tx_wavemailer_domain_model_mailqueue';

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
     * @throws Exception
     */
    public function getStatus(int $uid): ?string
    {
        $queryBuilder = $this->connectionPool->getConnectionForTable(self::TABLE_NAME)->createQueryBuilder();
        $result = $queryBuilder
            ->select('status')
            ->from(self::TABLE_NAME)
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, ParameterType::INTEGER)))
            ->executeQuery()
            ->fetchOne();

        return $result ?: null;
    }

    public function updateStatus(int $uid, string $status): void
    {
        $data = [
            'status' => $status,
            'tstamp' => time(),
        ];

        if ($status === MailQueue::STATUS_SENT) {
            $data['sent_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        }

        $this->connectionPool->getConnectionForTable(self::TABLE_NAME)->update(
            self::TABLE_NAME,
            $data,
            ['uid' => $uid]
        );
    }

    /**
     * @return array<int, int|string>
     * @throws Exception
     */
    public function findSubscriberUidsByPage(int $pageUid, ?string $status = null): array
    {
        $queryBuilder = $this->connectionPool->getConnectionForTable(self::TABLE_NAME)->createQueryBuilder();
        $queryBuilder
            ->select('subscriber_uid')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('page_uid', $queryBuilder->createNamedParameter($pageUid, ParameterType::INTEGER))
            );

        if ($status !== null) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('status', $queryBuilder->createNamedParameter($status))
            );
        }

        return $queryBuilder->executeQuery()->fetchFirstColumn();
    }

    /**
     * @throws Exception
     */
    public function incrementRetryCount(int $uid): int
    {
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE_NAME);

        $queryBuilder = $connection->createQueryBuilder();
        $result = $queryBuilder
            ->select('retry_count')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, ParameterType::INTEGER))
            )
            ->executeQuery()
            ->fetchAssociative();

        $currentRetryCount = (int)($result['retry_count'] ?? 0);
        $newRetryCount = $currentRetryCount + 1;

        $connection->update(
            self::TABLE_NAME,
            [
                'retry_count' => $newRetryCount,
                'tstamp' => time(),
            ],
            ['uid' => $uid]
        );

        return $newRetryCount;
    }
}
