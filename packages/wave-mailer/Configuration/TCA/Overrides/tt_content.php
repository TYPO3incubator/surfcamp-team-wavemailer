<?php

declare(strict_types=1);

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

    $sendManageSubscription = ExtensionUtility::registerPlugin(
        'WaveMailer',
        'SendManageSubscriptionLink',
        'Send Manage Subscription Link',
        'form-fieldset',
        'default',
        'Send the manage link to the user'
    );

    $contentType = 'wavemailer_textmedia';

    ExtensionManagementUtility::addTcaSelectItem(
        'tt_content',
        'CType',
        [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:contentElement.title',
            'description' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:contentElement.description',
            'value' => $contentType,
            'icon'  => 'content-textpic',
            'group' => 'default',
        ]
    );

    $GLOBALS['TCA']['tt_content']['types'][$contentType] = [
        'showitem' => '
            --div--;LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tabs.general,
            --palette--;;general,
            header;LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:contentElement.header,
            --palette--;;wavemailer_layout,
            bodytext;LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:contentElement.text;--palette--;;richtext,
            --div--;LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tabs.media,
            image,
            --div--;LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tabs.access,
            --palette--;;hidden,
            --palette--;;access,
        ',
        'columnsOverrides' => [
            'bodytext' => [
                'config' => [
                    'enableRichtext' => true,
                ]
            ],
            'image' => [
                'config' => [
                    'maxitems' => 1
                ]
            ],
            'textImageAlignment' => [
                'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.0',
                            'value' => 0,
                            'icon' => 'content-beside-text-img-above-center',
                        ],
                        [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.3',
                            'value' => 1,
                            'icon' => 'content-beside-text-img-below-center',
                        ],
                        [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.9',
                            'value' => 2,
                            'icon' => 'content-beside-text-img-right',
                        ],
                        [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.10',
                            'value' => 3,
                            'icon' => 'content-beside-text-img-left',
                        ],
                    ],
                    'default' => 0,
                    'fieldWizard' => [
                        'selectIcons' => [
                            'disabled' => false,
                        ],
                    ],
                ],
            ],
        ]
    ];

    $GLOBALS['TCA']['tt_content']['palettes']['wavemailer_layout'] = [
        'showitem' => 'textImageAlignment',
    ];
})();