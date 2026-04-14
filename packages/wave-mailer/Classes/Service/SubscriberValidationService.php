<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Service;

use Beffp\WaveMailer\Domain\Model\Subscriber;

class SubscriberValidationService
{
    public function isSubscriberEmailValid(Subscriber $subscriber): bool
    {
        return filter_var($subscriber->getEmail(), FILTER_VALIDATE_EMAIL) !== false;
    }
}