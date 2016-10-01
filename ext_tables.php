<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::insertModuleFunction(
        'web_info',
        \GeorgRinger\PageSpeed\Controller\ModFuncController::class,
        null,
        'LLL:EXT:page_speed/Resources/Private/Language/locallang.xlf:module'
    );
}
