<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Service;

use Beffp\WaveMailer\Domain\Model\MailQueue;
use Beffp\WaveMailer\Domain\Repository\MailQueueRepository;
use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;
use Beffp\WaveMailer\Queue\Message\SendNewsletterMessage;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

final class NewsletterQueueService
{
    private const NEWSLETTER_DOKTYPE = 116;

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly MailQueueRepository $mailQueueRepository,
        private readonly SubscriberRepository $subscriberRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly PersistenceManagerInterface $persistenceManager,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     * @throws Exception
     */
    public function findPendingNewsletterPages(): array
    {
        $now = time();
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');

        $pages = $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('doktype', self::NEWSLETTER_DOKTYPE),
                $queryBuilder->expr()->lte(
                    'tx_wavemailer_send_date',
                    $queryBuilder->createNamedParameter($now, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->gt(
                    'tx_wavemailer_send_date',
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        return array_filter($pages, function (array $page): bool {
            return !$this->isPageFullyProcessed((int)$page['uid']);
        });
    }

    /**
     * @return array<int>
     * @throws Exception
     */
    public function getSubscriptionGroupUidsForPage(int $pageUid): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_wavemailer_pages_subscriptiongroup_mm');

        $result = $queryBuilder
            ->select('uid_foreign')
            ->from('tx_wavemailer_pages_subscriptiongroup_mm')
            ->where(
                $queryBuilder->expr()->eq('uid_local', $queryBuilder->createNamedParameter($pageUid, ParameterType::INTEGER))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(fn(array $row): int => (int)$row['uid_foreign'], $result);
    }

    /**
     * @param array<int> $groupUids
     * @return array<int, array<string, mixed>>
     */
    public function findActiveSubscribers(int $pageUid, array $groupUids): array
    {
        $existingSubscriberUids = $this->mailQueueRepository->findSubscriberUidsByPage($pageUid);
        return $this->subscriberRepository->findActiveByGroups($groupUids, $existingSubscriberUids);
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws ExceptionInterface
     */
    public function createAndDispatchQueueEntry(int $pageUid, int $subscriberUid): void
    {
        $mailQueue = new MailQueue();
        $mailQueue->setPageUid($pageUid);
        $mailQueue->setSubscriberUid($subscriberUid);
        $mailQueue->setQueuedAt(new \DateTime());
        $mailQueue->setStatus(MailQueue::STATUS_QUEUED);
        $mailQueue->setRetryCount(0);

        $this->mailQueueRepository->add($mailQueue);
        $this->persistenceManager->persistAll();

        $message = new SendNewsletterMessage(
            $mailQueue->getUid(),
            $mailQueue->getPageUid(),
            $mailQueue->getSubscriberUid()
        );

        $this->messageBus->dispatch($message);
    }

    /**
     * @throws Exception
     */
    private function isPageFullyProcessed(int $pageUid): bool
    {
        $groupUids = $this->getSubscriptionGroupUidsForPage($pageUid);

        if (empty($groupUids)) {
            return true;
        }

        $sentSubscriberUids = $this->mailQueueRepository->findSubscriberUidsByPage($pageUid, MailQueue::STATUS_SENT);
        return $this->subscriberRepository->countActiveByGroups($groupUids, $sentSubscriberUids) === 0;
    }
}
