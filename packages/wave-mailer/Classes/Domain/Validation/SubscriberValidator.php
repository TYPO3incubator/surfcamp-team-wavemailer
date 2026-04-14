<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Domain\Validation;

use Beffp\WaveMailer\Domain\Model\Subscriber;
use Beffp\WaveMailer\Service\SubscriberValidationService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

final class SubscriberValidator extends AbstractValidator
{
    public function __construct(private readonly SubscriberValidationService $subscriberValidationService) {}

    protected function isValid(mixed $value): void
    {
        if (!$value instanceof Subscriber) {
            $errorString = 'The subscriber validator can only handle classes '
                . 'of type Beffp\WaveMailer\Domain\Model\Subscriber. '
                . $value::class . ' given instead.';
            $this->addError($errorString, 1776087687);
        }
        if (!$this->subscriberValidationService->isSubscriberEmailValid($value)) {
            $errorString = LocalizationUtility::translate(
                'error.Subscriber.invalidEmail',
                'WaveMailer',
            );
            $this->addErrorForProperty('email', $errorString, 1776087742);
        }
    }
}