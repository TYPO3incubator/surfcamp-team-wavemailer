<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class SubscriptionGroup extends AbstractEntity
{
    private string $name;

    /**
     * @var ObjectStorage<Subscriber>
     */
    private ObjectStorage $subscribers;

    public function __construct()
    {
        $this->subscribers = new ObjectStorage();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSubscribers(): ObjectStorage
    {
        return $this->subscribers;
    }

    public function setSubscribers(ObjectStorage $subscribers): void
    {
        $this->subscribers = $subscribers;
    }
}