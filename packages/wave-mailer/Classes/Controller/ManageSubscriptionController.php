<?php

namespace Beffp\WaveMailer\Controller;

use Beffp\WaveMailer\Domain\Model\Subscriber;
use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;
use Beffp\WaveMailer\Domain\Repository\SubscriptionGroupRepository;
use Beffp\WaveMailer\Domain\Validation\SubscriberValidator;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ManageSubscriptionController extends ActionController
{
    public function __construct(
        private readonly SubscriberRepository $subscriberRepository,
        private readonly SubscriptionGroupRepository $subscriptionGroupRepository,
    )
    {
    }

    public function indexAction(): ResponseInterface {
        return $this->htmlResponse();
    }

    public function sendManageLinkAction(string $email): ResponseInterface {
        $subscriber = $this->subscriberRepository->findOneBy(['email' => $email]);
        if ($subscriber === null) {
            return $this->htmlResponse();
        }

        $hash = hash('sha256', $subscriber->getEmail() . time());
        $subscriber->setManageSubscriptionToken($hash);

        $this->subscriberRepository->update($subscriber);

        $mangeSubscriptionEmail = new FluidEmail();
        $mangeSubscriptionEmail
            ->to($subscriber->getEmail())
            ->from(new Address($this->settings['fromAddress'], $this->settings['senderName']))
            ->subject(LocalizationUtility::translate('manageSubscription.subject', 'wave_mailer'))
            ->format(FluidEmail::FORMAT_BOTH)
            ->setTemplate('ManageSubscription')
            ->setRequest($this->request)
            ->assignMultiple(['subscriber' => $subscriber, 'settings' => $this->settings]);
        GeneralUtility::makeInstance(MailerInterface::class)->send($mangeSubscriptionEmail);

        return $this->htmlResponse();
    }

    public function manageAction(#[Validate(validator: SubscriberValidator::class)] ?Subscriber $subscriber = null, string $manageSubscriptionToken = ''): ResponseInterface
    {
        if($subscriber->getManageSubscriptionToken() !== $manageSubscriptionToken) {
            throw new \RuntimeException(LocalizationUtility::translate('error.generic', 'wave_mailer'), 1776346546);
        }

        $checkedGroups = [];
        if ($subscriber !== null) {
            foreach ($subscriber->getSubscriptionGroups() as $group) {
                $checkedGroups[$group->getUid()] = true;
            }
        }

        $this->view->assignMultiple([
            'subscriber' => $subscriber,
            'allSubscriptionGroups' => $this->subscriptionGroupRepository->findAll(),
            'checkedGroups' => $checkedGroups,
        ]);

        return $this->htmlResponse();
    }


    /**
     * @throws UnknownObjectException
     * @throws IllegalObjectTypeException
     */
    public function updateAction(#[Validate(validator: SubscriberValidator::class)] ?Subscriber $subscriber = null, string $manageSubscriptionToken = ''): ResponseInterface
    {
        if($subscriber->getManageSubscriptionToken() !== $manageSubscriptionToken) {
            throw new \RuntimeException(LocalizationUtility::translate('error.generic', 'wave_mailer'), 1776346546);
        }

        if ($subscriber === null) {
            throw new \RuntimeException(LocalizationUtility::translate('error.subscriber.notFound', 'wave_mailer'), 1776159682);
        }

        if ($subscriber->getCancellationDate()) {
            throw new \RuntimeException(LocalizationUtility::translate('error.subscription.cancelled', 'wave_mailer'), 1776159683);
        }

        $subscriber->setManageSubscriptionToken('');
        $this->subscriberRepository->update($subscriber);

        return $this->htmlResponse();
    }

    /**
     * @throws UnknownObjectException
     * @throws IllegalObjectTypeException
     */
    public function unsubscribeAction(string $email): ResponseInterface
    {
        /** @var Subscriber|null $subscriber */
        $subscriber = $this->subscriberRepository->findOneBy([
            'email' => $email,
        ]);

        if ($subscriber === null) {
            throw new \RuntimeException(LocalizationUtility::translate('error.subscriber.notFound', 'wave_mailer'), 1776159682);
        }

        if ($subscriber->getCancellationDate()) {
            throw new \RuntimeException(LocalizationUtility::translate('error.subscription.alreadyCancelled', 'wave_mailer'), 1776159683);
        }

        $subscriber->setCancellationDate(new \DateTime());
        $subscriber->setHidden(true);
        $subscriber->setManageSubscriptionToken('');
        $this->subscriberRepository->update($subscriber);

        return $this->htmlResponse();
    }
}
