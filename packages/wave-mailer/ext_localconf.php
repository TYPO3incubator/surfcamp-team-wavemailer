<?php

declare(strict_types=1);

use Beffp\WaveMailer\Controller\ManageSubscriptionController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::configurePlugin(
// extension name, matching the PHP namespaces (but without the vendor)
    'WaveMailer',
    'SubscriptionForm',
    [\Beffp\WaveMailer\Controller\SubscriptionController::class => 'form'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'WaveMailer',
    'ManageSubscription',
    [
        ManageSubscriptionController::class => 'index, update, unsubscribe',
    ],
    [
        ManageSubscriptionController::class => 'index',
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);
