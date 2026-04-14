<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

(static function (): void {
    $subscriptionForm = ExtensionUtility::registerPlugin(
    // extension name, matching the PHP namespaces (but without the vendor)
        'WaveMailer',
        // arbitrary, but unique plugin name (not visible in the backend)
        'SubscriptionForm',
        // plugin title, as visible in the drop-down in the backend, use "LLL:" for localization
        'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:subscriptionform.title',
        // plugin icon, use an icon identifier from the icon registry
        'form-fieldset',
        // plugin group, to define where the new plugin will be located in
        'default',
        // plugin description, as visible in the new content element wizard
        'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:subscriptionform.description',
        'FILE:EXT:wave_mailer/Configuration/FlexForms/Subscription.xml'
    );

    ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--div--;Configuration,pi_flexform,',
        $subscriptionForm,
        'after:subheader',
    );

    $manageSubscription = ExtensionUtility::registerPlugin(
        'WaveMailer',
        'ManageSubscription',
        'Manage Subscription',
        'form-fieldset',
        'default',
        'Manage newsletter subscription groups and unsubscribe'
    );
})();