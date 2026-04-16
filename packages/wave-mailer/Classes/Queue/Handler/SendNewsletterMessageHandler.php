<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Queue\Handler;

use Beffp\WaveMailer\Domain\Model\MailQueue;
use Beffp\WaveMailer\Domain\Repository\MailQueueRepository;
use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;
use Beffp\WaveMailer\Queue\Message\SendNewsletterMessage;
use Beffp\WaveMailer\Service\NewsletterPageRenderer;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Mail\MailMessage;

#[AsMessageHandler]
final class SendNewsletterMessageHandler
{
    private const MAX_RETRY_COUNT = 5;

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly NewsletterPageRenderer $pageRenderer,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
        private readonly ConnectionPool $connectionPool,
        private readonly MailQueueRepository $mailQueueRepository,
        private readonly SubscriberRepository $subscriberRepository,
    ) {}

    /**
     * @throws ExceptionInterface|Exception
     */
    public function __invoke(SendNewsletterMessage $message): void
    {
        $currentStatus = $this->mailQueueRepository->getStatus($message->mailQueueUid);
        if ($currentStatus === MailQueue::STATUS_SENT || $currentStatus === MailQueue::STATUS_FAILED) {
            return;
        }

        $subscriberEmail = $this->subscriberRepository->findEmailByUid($message->subscriberUid);
        if ($subscriberEmail === null) {
            $this->logger->error('Subscriber not found', [
                'subscriberUid' => $message->subscriberUid,
                'mailQueueUid' => $message->mailQueueUid,
            ]);
            return;
        }

        try {
            $htmlContent = $this->pageRenderer->render($message->pageUid);
            $pageTitle = $this->getPageTitle($message->pageUid);
            $this->sendEmail($subscriberEmail, $htmlContent, $pageTitle);
        } catch (\Throwable $exception) {
            $this->handleFailure($message, $subscriberEmail, $exception);
            return;
        }

        $this->mailQueueRepository->updateStatus($message->mailQueueUid, MailQueue::STATUS_SENT);

        $this->logger->info('Newsletter email sent successfully', [
            'email' => $subscriberEmail,
            'page_uid' => $message->pageUid,
            'mailQueueUid' => $message->mailQueueUid,
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    private function handleFailure(SendNewsletterMessage $message, string $email, \Throwable $exception): void
    {
        $newRetryCount = $this->mailQueueRepository->incrementRetryCount($message->mailQueueUid);

        $this->logger->error('Newsletter email sending failed', [
            'email' => $email,
            'page_uid' => $message->pageUid,
            'mailQueueUid' => $message->mailQueueUid,
            'retry_count' => $newRetryCount,
            'error' => $exception->getMessage(),
        ]);

        if ($newRetryCount < self::MAX_RETRY_COUNT) {
            $this->messageBus->dispatch(new SendNewsletterMessage(
                $message->mailQueueUid,
                $message->pageUid,
                $message->subscriberUid,
            ));
        } else {
            $this->mailQueueRepository->updateStatus($message->mailQueueUid, MailQueue::STATUS_FAILED);
        }
    }

    /**
     * @throws Exception
     */
    private function getPageTitle(int $pageUid): string
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $result = $queryBuilder
            ->select('title')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pageUid, ParameterType::INTEGER))
            )
            ->executeQuery()
            ->fetchAssociative();

        return $result['title'] ?? 'Newsletter';
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendEmail(string $email, string $htmlContent, string $subject): void
    {
        $fromAddress = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] ?? '';
        $fromName = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] ?? '';

        $mail = new MailMessage();
        $mail
            ->from(new Address($fromAddress, $fromName))
            ->to($email)
            ->subject($subject)
            ->html($htmlContent);

        $this->mailer->send($mail);
    }
}
