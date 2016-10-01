<?php

namespace GeorgRinger\PageSpeed\Service;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

class UrlService
{

    const VALID_ADDITIONAL_PARAMS = 'language';

    protected $paramMapping = [
        'language' => 'L'
    ];

    /**
     * @param int $pageId
     * @param array $additionalParams
     * @return string
     */
    public function getFullUrl($pageId, array $additionalParams = null)
    {
        $domainName = $this->getDomainName($pageId);
        // Mount point overlay: Set new target page id and mp parameter
        /** @var PageRepository $sysPage */
        $sysPage = GeneralUtility::makeInstance(PageRepository::class);
        $sysPage->init(false);
        $mountPointMpParameter = '';
        $finalPageIdToShow = $pageId;
        $mountPointInformation = $sysPage->getMountPointInfo($pageId);
        if ($mountPointInformation && $mountPointInformation['overlay']) {
            // New page id
            $finalPageIdToShow = $mountPointInformation['mount_pid'];
            $mountPointMpParameter = '&MP=' . $mountPointInformation['MPvar'];
        }
        // Modify relative path to protocol with host if domain record is given
        $protocolAndHost = '..';
        if ($domainName) {
            $protocol = 'http';
            $page = (array)$sysPage->getPage($finalPageIdToShow);
            if ($page['url_scheme'] == 2 || $page['url_scheme'] == 0 && GeneralUtility::getIndpEnv('TYPO3_SSL')) {
                $protocol = 'https';
            }
            $protocolAndHost = $protocol . '://' . $domainName;
        }

        $mountPointMpParameter .= $this->addAdditionalParams($additionalParams);

        $url = $protocolAndHost . '/index.php?id=' . $finalPageIdToShow . $this->getTypeParameterIfSet($finalPageIdToShow) . $mountPointMpParameter;
        return $url;
    }

    /**
     * @param array $additionalParams
     * @return string
     */
    protected function addAdditionalParams(array $additionalParams = null)
    {
        $extraParams = '';
        if (is_array($additionalParams) && !empty($additionalParams)) {
            $valid = explode(',', self::VALID_ADDITIONAL_PARAMS);
            foreach ($valid as $param) {
                if (isset($additionalParams[$param]) && $additionalParams[$param] != 0) {
                    $key = isset($this->paramMapping[$param]) ? $this->paramMapping[$param] : $param;
                    $extraParams .= sprintf('&%s=%s', $key, $additionalParams[$param]);
                }
            }
        }

        return $extraParams;
    }

    /**
     * With page TS config it is possible to force a specific type id via mod.web_view.type
     * for a page id or a page tree.
     * The method checks if a type is set for the given id and returns the additional GET string.
     *
     * @param int $pageId
     * @return string
     */
    protected function getTypeParameterIfSet($pageId)
    {
        $typeParameter = '';
        $modTSconfig = BackendUtility::getModTSconfig($pageId, 'mod.web_view');
        $typeId = (int)$modTSconfig['properties']['type'];
        if ($typeId > 0) {
            $typeParameter = '&type=' . $typeId;
        }
        return $typeParameter;
    }

    /**
     * Get domain name for requested page id
     *
     * @param int $pageId
     * @return string|NULL Domain name from first sys_domains-Record or from TCEMAIN.previewDomain, NULL if neither is configured
     */
    protected function getDomainName($pageId)
    {
        $previewDomainConfig = $this->getBackendUser()->getTSConfig('TCEMAIN.previewDomain', BackendUtility::getPagesTSconfig($pageId));
        if ($previewDomainConfig['value']) {
            $domain = $previewDomainConfig['value'];
        } else {
            $domain = BackendUtility::firstDomainRecord(BackendUtility::BEgetRootLine($pageId));
        }
        return $domain;
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
