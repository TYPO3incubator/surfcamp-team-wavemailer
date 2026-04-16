<?php

declare(strict_types=1);

use Beffp\WaveMailer\Controller\ManageSubscriptionController;
use Beffp\WaveMailer\Queue\Message\SendNewsletterMessage;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::configurePlugin(
// extension name, matching the PHP namespaces (but without the vendor)
    'WaveMailer',
    'SubscriptionForm',
    [\Beffp\WaveMailer\Controller\SubscriptionController::class => 'form, subscribe'],
    [\Beffp\WaveMailer\Controller\SubscriptionController::class => 'subscribe'],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'WaveMailer',
    'SendManageSubscriptionLink',
    [
        ManageSubscriptionController::class => 'index, sendManageLink',
    ],
    [
        ManageSubscriptionController::class => 'sendManageLink',
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'WaveMailer',
    'ManageSubscription',
    [
        ManageSubscriptionController::class => 'index, sendManageLink, manage, update, unsubscribe',
    ],
    [
        ManageSubscriptionController::class => 'manage, update, unsubscribe',
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][1776245886] = 'EXT:wave_mailer/Resources/Private/Templates/Email';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths'][1776245886] = 'EXT:wave_mailer/Resources/Private/Layouts/Email';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['messenger']['routing'][SendNewsletterMessage::class] = 'doctrine';
