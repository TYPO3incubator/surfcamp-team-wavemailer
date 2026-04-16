<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Commands;

use Beffp\WaveMailer\Service\NewsletterQueueService;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;

#[AsCommand(
    name: 'wavemailer:queue-emails',
    description: 'Queue scheduled emails.'
)]
final class SendMailsCommand extends Command
{
    public function __construct(
        private readonly NewsletterQueueService $queueService,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'batch-size',
            'b',
            InputOption::VALUE_OPTIONAL,
            'Maximum number of emails to queue per execution',
            50
        );
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchSize = (int)$input->getOption('batch-size');
        $pendingPages = $this->queueService->findPendingNewsletterPages();
        $totalCreated = 0;

        $pageCount = count($pendingPages);
        $this->logger->info('Found {count} newsletter pages to process', ['count' => $pageCount]);

        if ($pageCount === 0) {
            $this->logger->info('No newsletter pages found for sending');
            $output->writeln('Created 0 queue entries');
            return Command::SUCCESS;
        }

        foreach ($pendingPages as $page) {
            $pageUid = (int)$page['uid'];
            $groupUids = $this->queueService->getSubscriptionGroupUidsForPage($pageUid);
            $subscribers = $this->queueService->findActiveSubscribers($pageUid, $groupUids);
            $createdForPage = 0;

            foreach ($subscribers as $subscriber) {
                if ($totalCreated >= $batchSize) {
                    break 2;
                }

                $this->queueService->createAndDispatchQueueEntry($pageUid, (int)$subscriber['uid']);
                $totalCreated++;
                $createdForPage++;
            }

            $this->logger->info('Created {count} queue entries for page {pageUid}', [
                'count' => $createdForPage,
                'pageUid' => $pageUid,
            ]);
        }

        $output->writeln(sprintf('Created %d queue entries', $totalCreated));

        return Command::SUCCESS;
    }
}
