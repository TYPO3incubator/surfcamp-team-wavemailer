<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue',
        'label' => 'subscriber_uid',
        'label_alt' => 'page_uid,status',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'default_sortby' => 'queued_at DESC',
        'rootLevel' => 0,
        'hideTable' => true,
        'typeicon_classes' => [
            'default' => 'actions-envelope'
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'columns' => [
        'page_uid' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue.page_uid',
            'config' => [
                'type' => 'number',
                'size' => 10,
                'required' => true,
            ],
        ],
        'subscriber_uid' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue.subscriber_uid',
            'config' => [
                'type' => 'number',
                'size' => 10,
                'required' => true,
            ],
        ],
        'queued_at' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue.queued_at',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'readOnly' => true,
            ],
        ],
        'sent_at' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue.sent_at',
            'config' => [
                'type' => 'datetime',
                'dbType' => 'datetime',
                'readOnly' => true,
            ],
        ],
        'status' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue.status.queued',
                        'value' => 'queued',
                    ],
                    [
                        'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue.status.sent',
                        'value' => 'sent',
                    ],
                    [
                        'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue.status.failed',
                        'value' => 'failed',
                    ],
                ],
                'default' => 'queued',
                'readOnly' => true,
            ],
        ],
        'retry_count' => [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang_be.xlf:tx_wavemailer_domain_model_mailqueue.retry_count',
            'config' => [
                'type' => 'number',
                'size' => 5,
                'default' => 0,
                'readOnly' => true,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'page_uid, subscriber_uid, queued_at, sent_at, status, retry_count',
        ],
    ],
];
