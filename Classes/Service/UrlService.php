<?php

declare(strict_types=1);

namespace GeorgRinger\PageSpeed\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class UrlService
{

    /**
     * @param int $pageId
     * @param array $additionalParams
     * @return string
     */
    public static function getFullUrl(int $pageId, array $additionalParams = []): string
    {
        $additionalGetVars = '';
        if (isset($additionalParams['language'])) {
            $additionalGetVars .= '&L=' . $additionalParams['language'];
        }
        $url = BackendUtility::getPreviewUrl($pageId, '', null, '', '', $additionalGetVars);
        return $url;
    }

}
