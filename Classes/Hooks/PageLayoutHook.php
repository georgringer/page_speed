<?php

namespace GeorgRinger\PageSpeed\Hooks;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageLayoutHook
{
    /** @var SiteFinder */
    protected $siteFinder;

    public function __construct()
    {
        $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
    }

    public function render($params, PageLayoutController $pageLayoutController): string
    {
        $pageId = $pageLayoutController->id;

        return $this->renderDashboard($pageId);
    }

    protected function renderDashboard(int $pageId): string
    {
//

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addCssFile('EXT:page_speed/Resources/Public/Styles/PageModule.css');
//            $pageRenderer->loadRequireJsModule('TYPO3/CMS/GoogleAnalyticsReport/GaTest',
//            'function(GaTest) {
//                GaTest.setBasic(
//                    ' . GeneralUtility::quoteJSvalue($siteConfiguration['googleAnalyticsReportClientId']) . ',
//                    ' . GeneralUtility::quoteJSvalue($siteConfiguration['googleAnalyticsReportSiteId']) . ',
//                    "fo.com/bar"
//                );
//                GaTest.run();
//            }'
//        );
//        $currentUrl = $this->getFullUrl($pageId);

        return '
<div id="pagespeed-dashboard" xstyle="display: none">
<h1>xxxx</h1>
</div>
        ';
    }



}
