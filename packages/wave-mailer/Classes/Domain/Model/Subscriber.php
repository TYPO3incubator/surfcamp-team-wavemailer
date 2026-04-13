<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Subscriber extends AbstractEntity
{
    private string $firstName;

    private string $lastName;

    private string $email;

    /**
     * @var ObjectStorage<SubscriptionGroup>
     */
    private ObjectStorage $subscriptionGroups;

    private bool $doubleOptIn;

    public function __construct()
    {
        $this->subscriptionGroups = new ObjectStorage();
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSubscriptionGroups(): ObjectStorage
    {
        return $this->subscriptionGroups;
    }

    public function setSubscriptionGroups(ObjectStorage $subscriptionGroups): void
    {
        $this->subscriptionGroups = $subscriptionGroups;
    }

    public function isDoubleOptIn(): bool
    {
        return $this->doubleOptIn;
    }

    public function setDoubleOptIn(bool $doubleOptIn): void
    {
        $this->doubleOptIn = $doubleOptIn;
    }
}
