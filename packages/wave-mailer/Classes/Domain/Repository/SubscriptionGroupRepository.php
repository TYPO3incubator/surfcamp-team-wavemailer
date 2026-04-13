<?php

namespace Beffp\WaveMailer\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class SubscriptionGroupRepository extends Repository
{
    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }
}