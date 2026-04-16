<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Queue\Message;

final readonly class SendNewsletterMessage
{
    public function __construct(
        public int $mailQueueUid,
        public int $pageUid,
        public int $subscriberUid,
    ) {}
}
