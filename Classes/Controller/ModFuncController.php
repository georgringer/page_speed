<?php

namespace GeorgRinger\PageSpeed\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use GeorgRinger\PageSpeed\Domain\Model\Dto\Configuration;
use GeorgRinger\PageSpeed\Domain\Repository\PageSpeedRepository;
use GeorgRinger\PageSpeed\Service\UrlService;
use TYPO3\CMS\Backend\Module\AbstractFunctionModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ModFuncController extends AbstractFunctionModule
{

    /** @var StandaloneView */
    protected $view;

    /** @var string */
    protected $templatePath = 'EXT:page_speed/Resources/Private/Templates/';

    /** @var UrlService */
    protected $urlService;

    /** @var PageSpeedRepository */
    protected $pageSpeedRepository;

    /** @var Configuration */
    protected $configuration;

    /** @var int */
    protected $pageId = 0;

    public function __construct()
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->getRequest()->setControllerExtensionName('lowlevel');
        $this->pageId = (int)GeneralUtility::_GET('id');
        $this->urlService = new UrlService();
        $this->pageSpeedRepository = new PageSpeedRepository();
        $this->configuration = new Configuration();

        $this->addScripts();
    }

    /**
     * Function menu initialization
     *
     * @return array Menu array
     */
    public function modMenu()
    {
        $languages = [];
        $languageRecords = $this->getDatabaseConnection()->exec_SELECTgetRows('uid,title', 'sys_language', 'hidden=0', '', 'title');
        if (is_array($languageRecords) && !empty($languageRecords)) {
            $defaultLanguageLabel = BackendUtility::getModTSconfig($this->pObj->id, 'mod.SHARED.defaultLanguageLabel');
            $languages[] = $defaultLanguageLabel['value'] ?:  'Default';
            foreach ($languageRecords as $language) {
                $languages[$language['uid']] = $language['title'];
            }
        }

        $modMenuAdd = [
            'language' => $languages
        ];
        return $modMenuAdd;
    }

    public function main()
    {
        $result = $error = $url = null;

        $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePath . 'Main.html'));

        if ($this->configuration->isValid()) {
            try {
                $this->checkPageId();
                $url = $this->urlService->getFullUrl($this->pageId, $this->pObj->MOD_SETTINGS);

                if (GeneralUtility::_GET('clear')) {
                    $this->pageSpeedRepository->clearByIdentifier($url);
                    $this->view->assign('cacheCleared', true);
                }

                $result = $this->pageSpeedRepository->findByIdentifier($url);
            } catch (\HTTP_Request2_ConnectionException $e) {
                $error = 'error.http_request.connection';
                // todo add log
            } catch (\RuntimeException $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = 'error.invalid.key';
        }

        $this->view->assignMultiple([
            'lll' => 'LLL:EXT:page_speed/Resources/Private/Language/locallang.xlf:',
            'menu' => $this->modifyFuncMenu(BackendUtility::getFuncMenu(
                $this->pObj->id,
                'SET[language]',
                $this->pObj->MOD_SETTINGS['language'],
                $this->pObj->MOD_MENU['language']
            ), 'language'),
            'configuration' => $this->configuration,
            'result' => $result,
            'url' => $url,
            'error' => $error,
            'pageId' => $this->pageId
        ]);

        return $this->view->render();
    }

    /**
     * Check if the page id is valid
     *
     * @return void
     */
    protected function checkPageId()
    {
        if ($this->pageId === 0) {
            throw new \UnexpectedValueException('error.page.idIsZero');
        }
        $row = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'pages', 'uid=' . $this->pageId);

        if (!GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['FE']['content_doktypes'], $row['doktype'])) {
            throw new \UnexpectedValueException('error.page.doktype');
        }
        if ($row['hidden'] == 1 || $row['deleted'] == 1) {
            throw new \UnexpectedValueException('error.page.hidden');
        }
        if (!empty($row['fe_group'])) {
            throw new \UnexpectedValueException('error.page.restricted');
        }
    }

    /**
     * Hack some bootstrap logic into the core
     *
     * @param string $code
     * @param string $id
     * @return string
     */
    protected function modifyFuncMenu($code, $id)
    {
        return str_replace('<select', '<select class="form-control" id="' . htmlspecialchars($id) . '"', $code);
    }

    /**
     * Add JS and CSS files
     *
     * @return void
     */
    protected function addScripts()
    {
        $path = ExtensionManagementUtility::extRelPath('page_speed') . 'Resources/Public/';
        $this->getDocumentTemplate()->addStyleSheet('page_speed', $path . 'Styles/speed.css');
        $jsFiles = ['js/main.js', 'Contrib/amcharts/amcharts.js', 'Contrib/amcharts/gauge.js', 'Contrib/amcharts/serial.js', 'Contrib/amcharts/themes/dark.js'];
        foreach ($jsFiles as $file) {
            $this->getDocumentTemplate()->loadJavascriptLib($path . $file);
        }
    }
}
