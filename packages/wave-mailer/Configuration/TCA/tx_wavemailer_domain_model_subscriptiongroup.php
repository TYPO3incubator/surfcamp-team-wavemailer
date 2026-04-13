<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriptiongroup',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'default_sortby' => 'name',
        'rootLevel' => 0,
        'typeicon_classes' => [
            'default' => 'status-user-group-frontend'
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
        'name' => [
           'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer_domain_model_subscriptiongroup.name',
           'config' => [
               'type' => 'input',
               'required' => true,
           ],
        ],
        'subscribers' => [
            'label' => 'Subscribers',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_wavemailer_domain_model_subscriber',
                'foreign_table' => 'tx_wavemailer_domain_model_subscriber',
                'MM' => 'tx_wavemailer_domain_model_subscriptiongroup_subscriber_mm',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'name, subscribers',
        ],
    ],
];
