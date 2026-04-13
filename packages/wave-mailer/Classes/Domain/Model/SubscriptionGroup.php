<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class SubscriptionGroup extends AbstractEntity
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}