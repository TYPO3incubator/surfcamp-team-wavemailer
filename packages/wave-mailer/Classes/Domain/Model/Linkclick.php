<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Linkclick extends AbstractEntity
{
    protected int $newsletter = 0;

    protected int $targetPid = 0;

    protected string $link = '';

    protected ?\DateTime $time = null;

    public function getNewsletter(): int
    {
        return $this->newsletter;
    }

    public function setNewsletter(int $newsletter): void
    {
        $this->newsletter = $newsletter;
    }

    public function getTargetPid(): int
    {
        return $this->targetPid;
    }

    public function setTargetPid(int $targetPid): void
    {
        $this->targetPid = $targetPid;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setTime(?\DateTime $time): void
    {
        $this->time = $time;
    }
}
