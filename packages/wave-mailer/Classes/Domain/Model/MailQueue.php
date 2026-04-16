<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class MailQueue extends AbstractEntity
{
    public const STATUS_QUEUED = 'queued';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    protected int $pageUid = 0;

    protected int $subscriberUid = 0;

    protected ?\DateTime $queuedAt = null;

    protected ?\DateTime $sentAt = null;

    protected string $status = self::STATUS_QUEUED;

    protected int $retryCount = 0;

    public function getPageUid(): int
    {
        return $this->pageUid;
    }

    public function setPageUid(int $pageUid): void
    {
        $this->pageUid = $pageUid;
    }

    public function getSubscriberUid(): int
    {
        return $this->subscriberUid;
    }

    public function setSubscriberUid(int $subscriberUid): void
    {
        $this->subscriberUid = $subscriberUid;
    }

    public function getQueuedAt(): ?\DateTime
    {
        return $this->queuedAt;
    }

    public function setQueuedAt(?\DateTime $queuedAt): void
    {
        $this->queuedAt = $queuedAt;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTime $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function setRetryCount(int $retryCount): void
    {
        $this->retryCount = $retryCount;
    }
}
