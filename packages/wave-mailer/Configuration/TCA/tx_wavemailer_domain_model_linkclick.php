<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_linkclick',
        'label' => 'link',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'default_sortby' => 'time DESC',
        'rootLevel' => 0,
        'typeicon_classes' => [
            'default' => 'actions-link'
        ],
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'hideTable' => true
    ],
    'columns' => [
        'newsletter' => [
            'exclude' => true,
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_linkclick.newsletter',
            'config' => [
                'type' => 'number',
                'size' => 10,
            ],
        ],
        'target_pid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_linkclick.target_pid',
            'config' => [
                'type' => 'number',
                'size' => 10,
            ],
        ],
        'link' => [
            'exclude' => true,
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_linkclick.link',
            'config' => [
                'type' => 'link',
                'size' => 30,
                'required' => true,
            ],
        ],
        'time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_linkclick.time',
            'config' => [
                'type' => 'datetime',
                'required' => true,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'newsletter, target_pid, link, time, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden',
        ],
    ],
];
