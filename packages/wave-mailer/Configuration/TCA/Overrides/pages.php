<?php

defined('TYPO3') or die();

(function () {
    $customPageDoktype = '116';
    $customIconClass = 'tx-wavemailer-newsletter-page';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        'pages',
        'doktype',
        [
            'label' => 'LLL:EXT:wave_mailer/Resources/Private/Language/locallang.xlf:tx_wavemailer.pagetype',
            'value' => $customPageDoktype,
            'icon'  => $customIconClass,
            'group' => 'special',
        ],
    );

    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$customPageDoktype] = $customIconClass;
    $GLOBALS['TCA']['pages']['types']['116'] = $GLOBALS['TCA']['pages']['types']['1'];
})();
