<?php

namespace Beffp\WaveMailer\Controller;

use Beffp\WaveMailer\Domain\Model\Subscriber;
use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;
use Beffp\WaveMailer\Domain\Validation\SubscriberValidator;
use Beffp\WaveMailer\Exception\SettingsException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;

class SubscriptionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    public function __construct(protected readonly SubscriberRepository $subscriberRepository)
    {
    }

    /**
     * renders the subscription form
     *
     * @return ResponseInterface
     */
    public function formAction(): ResponseInterface
    {
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
        $this->subscriberRepository->add($newSubscriber);

        if(!isset($this->settings['confirmationPage']) || $this->settings['confirmationPage'] === 0) {
            throw new SettingsException('Confirmation page setting is missing!', 1776089125);
        }

        $uri = $this->uriBuilder->setTargetPageUid($this->settings['confirmationPage'])->build();

        return $this->redirectToUri($uri);
    }
}
