<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Beffp\WaveMailer\Controller\AnalyticsController;
use Beffp\WaveMailer\Controller\BackendController;
use T3docs\Examples\Controller\AdminModuleController;
use T3docs\Examples\Controller\ModuleController;

return [
    'wavemailer_analytics' => [
        'parent' => 'content',
        'position' => ['after' => '*'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/tx_wavemailer',
        'navigationComponent' => null,
        'inheritNavigationComponentFromMainModule' => false,
        'extensionName' => 'WaveMailer',
        'iconIdentifier' => 'tx-wavemailer-newsletter-page',
        'labels' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_mod.xlf',
        'routes' => [
            '_default' => [
                'target' => BackendController::class . '::indexAction',
            ],
        ],
    ],
];