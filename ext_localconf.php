<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Cache for the calls
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['page_speed_response'])) {
    $configuration = new \GeorgRinger\PageSpeed\Domain\Model\Dto\Configuration();

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['page_speed_response'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\StringFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
        'options' => [
            'defaultLifetime' => $configuration->getCacheTime(),
        ],
//		'groups' => array('system')
    ];
}
