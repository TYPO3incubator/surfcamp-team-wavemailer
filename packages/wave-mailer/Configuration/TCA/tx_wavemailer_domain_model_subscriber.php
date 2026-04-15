<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'default_sortby' => 'email',
        'rootLevel' => 0,
        'typeicon_classes' => [
            'default' => 'actions-user'
        ],
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'columns' => [
        'email' => [
           'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.email',
           'config' => [
               'type' => 'email',
               'eval' => 'unique',
               'required' => true,
           ],
        ],
        'salutation' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.salutation',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.salutation.notGiven', 0],
                    ['LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.salutation.mr', 1],
                    ['LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.salutation.ms', 2],
                    ['LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.salutation.other', 3],
                ],
            ],
        ],
        'first_name' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.first_name',
            'config' => [
                'type' => 'input',
            ],
        ],
        'last_name' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.last_name',
            'config' => [
                'type' => 'input',
            ],
        ],
        'double_opt_in' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.double_opt_in',
            'config' => [
                'type' => 'check',
                'items' => [
                    [
                        'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.double_opt_in.item.label',
                    ],
                ],
            ],
        ],
        'double_opt_in_token' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.double_opt_in_token',
            'config' => [
                'type' => 'input',
                'readOnly' => true
            ],
        ],
        'subscription_groups' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_subscriber.subscription_groups',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_wavemailer_domain_model_subscriptiongroup',
                'foreign_table' => 'tx_wavemailer_domain_model_subscriptiongroup',
                'MM' => 'tx_wavemailer_domain_model_subscriptiongroup_subscriber_mm',
                'MM_opposite_field' => 'subscribers'
            ],
        ],
        'cancellation_date' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriber.cancellation_date',
            'config' => [
                'type' => 'datetime',
                'readOnly' => true,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'email, first_name, last_name, double_opt_in, subscription_groups, cancellation_date',
        ],
    ],
];
