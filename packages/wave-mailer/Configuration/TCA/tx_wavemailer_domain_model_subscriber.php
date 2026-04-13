<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriber',
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
           'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriber.email',
           'config' => [
               'type' => 'email',
               'eval' => 'unique',
               'required' => true,
           ],
        ],
        'first_name' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriber.first_name',
            'config' => [
                'type' => 'text',
            ],
        ],
        'last_name' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriber.last_name',
            'config' => [
                'type' => 'text',
            ],
        ],
        'double_opt_in' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriber.double_opt_in',
            'config' => [
                'type' => 'check',
                'items' => [
                    [
                        'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriber.double_opt_in.item.label',
                    ],
                ],
            ],
        ],
        'subscription_groups' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriber.subscription_groups',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_wavemailer_domain_model_subscriptiongroup',
                'foreign_table' => 'tx_wavemailer_domain_model_subscriptiongroup',
                'MM' => 'tx_wavemailer_domain_model_subscriptiongroup_subscriber_mm',
                'MM_opposite_field' => 'subscribers'
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'email, first_name, last_name, double_opt_in, subscription_groups',
        ],
    ],
];
