<?php

namespace Beffp\WaveMailer\Controller;

use Beffp\WaveMailer\Domain\Model\Subscriber;
use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;
use Beffp\WaveMailer\Domain\Repository\SubscriptionGroupRepository;
use Beffp\WaveMailer\Domain\Validation\SubscriberValidator;
use Beffp\WaveMailer\Exception\SettingsException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Domain\RecordFactory;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;

class SubscriptionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    public function __construct(
        protected RecordFactory $recordFactory,
        protected readonly SubscriberRepository $subscriberRepository,
        protected readonly SubscriptionGroupRepository $subscriptionGroupRepository,
    ) {}
    public function formAction(#[IgnoreValidation] ?Subscriber $newSubscriber = null): ResponseInterface
    {
        $cObj = $this->request->getAttribute('currentContentObject');
        $record = $this->recordFactory->createResolvedRecordFromDatabaseRow('tt_content', $cObj->data);
        $this->view->assign('record', $record);

        if(isset($this->settings['subscriptionGroups'])) {
            $subscriptionGroups = array_map(fn($id) => $this->subscriptionGroupRepository->findOneBy(['uid' => $id]), explode(',', $this->settings['subscriptionGroups']));
        } else {
            $subscriptionGroups = $this->subscriptionGroupRepository->findAll();
        }

        $this->view->assign('subscriptionGroups', $subscriptionGroups);
        $this->view->assign('subscriber', $newSubscriber);

        return $this->htmlResponse();
    }

    /**
     * creates a new subscriber
     *
     * @param Subscriber $newSubscriber
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     * @throws SettingsException
     */
    public function subscribeAction(#[Validate(validator: SubscriberValidator::class)] Subscriber $newSubscriber): ResponseInterface
    {
        if(!isset($this->settings['fromAddress']) || $this->settings['fromAddress'] === '') {
            throw new SettingsException('The sender address is missing!', 1776245299);
        }

        if(!isset($this->settings['senderName']) || $this->settings['senderName'] === '') {
            throw new SettingsException('The sender name is missing!', 1776245423);
        }

        if(!isset($this->settings['confirmationPage']) || $this->settings['confirmationPage'] === 0) {
            throw new SettingsException('Confirmation page setting is missing!', 1776089125);
        }

        $hash = hash('sha256', $newSubscriber->getEmail() . time());
        $newSubscriber->setDoubleOptInToken($hash);

        $this->subscriberRepository->add($newSubscriber);

        $doubleOptInEmail = new FluidEmail();
        $doubleOptInEmail
            ->to($newSubscriber->getEmail())
            ->from(new Address($this->settings['fromAddress'], $this->settings['senderName']))
            ->subject('Please confirm your subscription')
            ->format(FluidEmail::FORMAT_BOTH) // send HTML and plaintext mail
            ->setTemplate('DoubleOptIn')
            ->setRequest($this->request)
            ->assignMultiple(['subscriber' => $newSubscriber, 'settings' => $this->settings]);
        GeneralUtility::makeInstance(MailerInterface::class)->send($doubleOptInEmail);

        $uri = $this->uriBuilder->setTargetPageUid($this->settings['confirmationPage'])->build();

        return $this->redirectToUri($uri);
    }
}
