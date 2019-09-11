<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Cache for the calls
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['page_speed_response'])) {
    $configuration = new \GeorgRinger\PageSpeed\Domain\Model\Dto\Configuration();

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['page_speed_response'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
        'options' => [
            'defaultLifetime' => $configuration->getCacheTime(),
        ],
//		'groups' => array('system')
    ];
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][] = \GeorgRinger\PageSpeed\Hooks\PageLayoutHook::class . '->render';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook'][] =
    \GeorgRinger\PageSpeed\Hooks\ButtonBarHook::class . '->getButtons';

$icons = [
    'ext-pagespeed-mobile' => 'mobile.svg',
    'ext-pagespeed-desktop' => 'desktop.svg',
];
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
foreach ($icons as $identifier => $path) {
    $iconRegistry->registerIcon(
        $identifier,
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:page_speed/Resources/Public/Icons/' . $path]
    );
}
