<?php

namespace GeorgRinger\PageSpeed\Controller;

use GeorgRinger\PageSpeed\Domain\Model\Dto\Configuration;
use GeorgRinger\PageSpeed\Domain\Repository\PageSpeedRepository;
use TYPO3\CMS\Backend\Module\BaseScriptClass;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ModFuncController
{
    /**
     * Contains a reference to the parent (calling) object (which is probably an instance of
     * an extension class to \TYPO3\CMS\Backend\Module\BaseScriptClass
     *
     * @var BaseScriptClass
     * @see init()
     */
    public $pObj;

    /** @var StandaloneView */
    protected $view;

    /** @var string */
    protected $templatePath = 'EXT:page_speed/Resources/Private/Templates/';

    /** @var PageSpeedRepository */
    protected $pageSpeedRepository;

    /** @var Configuration */
    protected $configuration;

    /** @var int */
    protected $pageId = 0;


    /**
     * Initialize the object
     *
     * @param \object $pObj A reference to the parent (calling) object
     * @throws \RuntimeException
     * @see \TYPO3\CMS\Backend\Module\BaseScriptClass::checkExtObj()
     */
    public function init($pObj)
    {
        $this->pObj = $pObj;
        $this->pObj->MOD_MENU = array_merge($this->pObj->MOD_MENU, $this->modMenu());
    }

    public function __construct()
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->getRequest()->setControllerExtensionName('page_speed');
        $this->pageId = (int)GeneralUtility::_GET('id');
        $this->pageSpeedRepository = GeneralUtility::makeInstance(PageSpeedRepository::class);
        $this->configuration = GeneralUtility::makeInstance(Configuration::class);

        $this->addScripts();
    }

    /**
     * Function menu initialization
     *
     * @return array Menu array
     */
    public function modMenu(): array
    {
        $languages = [];
        $languageRecords = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_language')
            ->select('*')
            ->from('sys_language')
            ->execute()
            ->fetchAll();
        if (\is_array($languageRecords) && !empty($languageRecords)) {
            $defaultLanguageLabel = BackendUtility::getModTSconfig($this->pObj->id, 'mod.SHARED.defaultLanguageLabel');
            $languages[] = $defaultLanguageLabel['value'] ?: 'Default';
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
                $url = $this->getFullUrl($this->pageId, $this->pObj->MOD_SETTINGS);

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
            'menu' => BackendUtility::getFuncMenu(
                $this->pObj->id,
                'SET[language]',
                $this->pObj->MOD_SETTINGS['language'],
                $this->pObj->MOD_MENU['language']
            ),
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
    protected function checkPageId(): void
    {
        if ($this->pageId === 0) {
            throw new \UnexpectedValueException('error.page.idIsZero');
        }
        $row = BackendUtility::getRecord('pages', $this->pageId);

        if ($row['hidden'] == 1 || $row['deleted'] == 1) {
            throw new \UnexpectedValueException('error.page.hidden');
        }
        if (!empty($row['fe_group'])) {
            throw new \UnexpectedValueException('error.page.restricted');
        }
    }

    /**
     * Add JS and CSS files
     *
     * @return void
     */
    protected function addScripts()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addCssFile('EXT:page_speed/Resources/Public/Styles/speed.css');

        $jsFiles = ['JavaScript/main.js', 'Contrib/amcharts/amcharts.js', 'Contrib/amcharts/gauge.js', 'Contrib/amcharts/serial.js', 'Contrib/amcharts/themes/dark.js'];
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        foreach ($jsFiles as $file) {
            $pageRenderer->addJsFile('EXT:page_speed/Resources/Public/' . $file);
        }
    }

    /**
     * @param int $pageId
     * @param array $additionalParams
     * @return string
     */
    protected function getFullUrl($pageId, array $additionalParams = []): string
    {
        $additionalGetVars = '';
        if (isset($additionalParams['language'])) {
            $additionalGetVars .= '&L=' . $additionalParams['language'];
        }
        $url = BackendUtility::getPreviewUrl($pageId, '', null, '', '', $additionalGetVars);
        return $url;
    }

}
