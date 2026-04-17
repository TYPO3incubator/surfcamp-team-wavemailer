<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Domain\Validation;

use Beffp\WaveMailer\Domain\Model\Subscriber;
use Beffp\WaveMailer\Service\SubscriberValidationService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

final class SubscriberValidator extends AbstractValidator
{
    public function __construct(private readonly SubscriberValidationService $subscriberValidationService)
    {
    }

    protected $supportedOptions = [
        'validateSettings' => [false, 'Weather to check settings for required fields', 'boolean'],
    ];

    protected function isValid(mixed $value): void
    {
        if (!$value instanceof Subscriber) {
            $errorString = 'The subscriber validator can only handle classes '
                . 'of type Beffp\WaveMailer\Domain\Model\Subscriber. '
                . $value::class . ' given instead.';
            $this->addError($errorString, 1776087687);
        }
        if (!$this->subscriberValidationService->isSubscriberEmailValid($value)) {
            $this->addErrorForProperty('email', LocalizationUtility::translate(
                'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:error.Subscriber.invalidEmail',
                'WaveMailer',
            ), 1776087742);
        }
        if ($this->options['validateSettings']) {
            /** @var Site $site */
            $site = $this->request->getAttribute('site');

            if($site->getSettings()->get('waveMailer.requireFirstName', false)) {
                if (!$this->subscriberValidationService->hasFirstName($value)) {
                    $this->addErrorForProperty('firstName', LocalizationUtility::translate(
                        'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:error.Subscriber.noFirstName',
                    ), 1776262924);
                }
            }

            if($site->getSettings()->get('waveMailer.requireLastName', false)) {
                if (!$this->subscriberValidationService->hasLastName($value)) {
                    $this->addErrorForProperty('lastName', LocalizationUtility::translate(
                        'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:error.Subscriber.noLastName',
                    ), 1776262928);
                }
            }
        }
    }
}