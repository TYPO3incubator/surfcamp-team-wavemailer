<?php

return [
    'ctrl' => [
        'title' => 'Subscriber', // @TODO replace with LLL
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'default_sortby' => 'email',
        'rootLevel' => 0,
        'iconfile' => 'EXT:wave_mailer/Resources/Public/Icons/subscriber.svg',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
];
