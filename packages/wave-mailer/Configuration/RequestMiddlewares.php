<?php

defined('TYPO3') or die();


return [
    'frontend' => [
        'middleware-identifier' => [
            'target' => \Beffp\WaveMailer\Middleware\LinkTrackingMiddleware::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
    ],
];