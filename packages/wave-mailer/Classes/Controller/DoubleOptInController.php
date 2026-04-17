<?php

namespace Beffp\WaveMailer\Controller;

use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class DoubleOptInController extends ActionController
{
    public function __construct(protected readonly SubscriberRepository $subscriberRepository)
    {
    }

    public function confirmAction(string $hash) {
        if ($hash === '') {
            throw new \InvalidArgumentException(LocalizationUtility::translate('error.hashEmpty', 'wave_mailer'), 1776157052);
        }

        $subscriber = $this->subscriberRepository->findOneBy(['doubleOptInToken' => $hash]);

        if ($subscriber === null) {
            $this->view->assign('message', 'doubleOptIn.userNotFound');
        } else {
            $subscriber->setDoubleOptIn(true);
            $this->subscriberRepository->update($subscriber);
            $this->view->assign('message', 'doubleOptIn.confirmed');
        }

        return $this->htmlResponse();
    }
}