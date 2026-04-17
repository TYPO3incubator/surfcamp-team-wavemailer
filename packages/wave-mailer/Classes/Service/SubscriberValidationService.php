<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Service;

use Beffp\WaveMailer\Domain\Model\Subscriber;
use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;

class SubscriberValidationService
{
    public function __construct(protected readonly SubscriberRepository $subscriberRepository)
    {
    }

    public function isSubscriberEmailValid(Subscriber $subscriber): bool
    {
        return filter_var($subscriber->getEmail(), FILTER_VALIDATE_EMAIL) !== false;
    }

    public function isSubscriberEmailUnique(Subscriber $subscriber): bool
    {
        return $this->subscriberRepository->count(['email' => $subscriber->getEmail(), 'cancellationDate' => 0]) < 1;
    }

    public function hasFirstName(Subscriber $subscriber): bool
    {
        return $subscriber->getFirstName() !== '';
    }

    public function hasLastName(Subscriber $subscriber): bool
    {
        return $subscriber->getLastName() !== '';
    }
}