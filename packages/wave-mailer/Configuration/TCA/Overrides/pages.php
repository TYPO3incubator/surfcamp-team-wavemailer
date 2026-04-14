<?php

defined('TYPO3') or die();

(function () {
    $customPageDoktype = '116';
    $customIconClass = 'tx-wavemailer-newsletter-page';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        'pages',
        'doktype',
        [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer.pagetype',
            'value' => $customPageDoktype,
            'icon'  => $customIconClass,
            'group' => 'special',
        ],
    );

    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$customPageDoktype] = $customIconClass;
    $GLOBALS['TCA']['pages']['types']['116'] = $GLOBALS['TCA']['pages']['types']['1'];

    $GLOBALS['TCA']['pages']['columns']['tx_wavemailer_subscription_groups'] = [
        'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:pages.tx_wavemailer_subscription_groups',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectMultipleSideBySide',
            'foreign_table' => 'tx_wavemailer_domain_model_subscriptiongroup',
            'MM' => 'tx_wavemailer_pages_subscriptiongroup_mm',
            'size' => 5,
            'autoSizeMax' => 10,
        ],
    ];

    $GLOBALS['TCA']['pages']['columns']['tx_wavemailer_send_date'] = [
        'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:pages.tx_wavemailer_send_date',
        'config' => [
            'type' => 'datetime',
            'format' => 'datetime',
            'default' => 0,
        ],
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'pages',
        'tx_wavemailer_subscription_groups,tx_wavemailer_send_date',
        $customPageDoktype,
        'after:nav_title'
    );

    $GLOBALS['TCA']['pages']['types']['116']['wizardSteps'] = [
        'setup' => [
            'title' => 'LLL:EXT:backend/Resources/Private/Language/Wizards/page.xlf:step.setup',
            'fields' => ['title', 'slug', 'nav_title', 'hidden', 'nav_hide'],
        ],
        'wave_mailer' => [
            'title' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:pages.wizard.wave_mailer_step',
            'fields' => ['tx_wavemailer_subscription_groups', 'tx_wavemailer_send_date'],
            'after' => ['setup'],
        ],
    ];
})();
