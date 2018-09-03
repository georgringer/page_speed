<?php

namespace GeorgRinger\PageSpeed\Domain\Repository;

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
use GeorgRinger\PageSpeed\Domain\Model\Response;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\HttpRequest;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageSpeedRepository
{

    /** @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface */
    protected $cache;

    /** @var Configuration */
    protected $configuration;

    /** @var array */
    protected $strategies = ['desktop', 'mobile'];

    public function __construct()
    {
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('page_speed_response');
        $this->configuration = GeneralUtility::makeInstance(Configuration::class);
    }

    /**
     * @param string $identifier
     * @return array<Response>
     */
    public function findByIdentifier($identifier)
    {
        $apiResponses = $result = [];

        $locale = $this->getLocale();
        foreach ($this->strategies as $strategy) {
            if ($this->configuration->isDemo()) {
                $path = ExtensionManagementUtility::extPath('page_speed', 'Resources/Private/Examples/');
                $apiResponses[$strategy] = GeneralUtility::getUrl($path . $strategy . '.json');
            } else {
                $cacheIdentifier = $this->getCacheIdentifier($identifier, $strategy);

                $resultFromApi = $this->getFromCache($cacheIdentifier);
                if (!$resultFromApi) {
                    $resultFromApi = $this->getResponseFromApi($identifier, $strategy, $locale);
                    $this->setToCache($cacheIdentifier, $resultFromApi);
                }
                $apiResponses[$strategy] = $resultFromApi;
            }
        }

        foreach ($apiResponses as $strategy => $response) {
            $result[$strategy] = new Response($response);
        }
        return $result;
    }

    /**
     * @param string $identifier
     * @return void
     * @todo what to do with the locale
     */
    public function clearByIdentifier($identifier)
    {
        foreach ($this->strategies as $strategy) {
            $cacheIdentifier = $this->getCacheIdentifier($identifier, $strategy);
            $this->cache->remove($cacheIdentifier);
        }
    }

    /**
     * @param string $url
     * @param string $strategy
     * @return string
     */
    protected function getCacheIdentifier($url, $strategy)
    {
        $locale = $this->getLocale();
        $cacheIdentifier = $url . '_' . implode('_', [$strategy, $locale]);
        return md5($cacheIdentifier);
    }

    /**
     * @param string $identifier
     * @param string $strategy
     * @param string $locale
     * @return string
     */
    protected function getResponseFromApi($identifier, $strategy, $locale = 'en')
    {
        $url = sprintf('https://www.googleapis.com/pagespeedonline/v4/runPagespeed?screenshot=true&url=%s&strategy=%s&locale=%s&key=%s',
            rawurlencode($identifier),
            $strategy,
            $locale,
            $this->configuration->getKey());
        return $this->apiCall($url);
    }

    /**
     * Call the API with given url
     *
     * @param string $url
     * @return string
     */
    protected function apiCall($url)
    {
        // Initiate the Request Factory, which allows to run multiple requests
        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $additionalOptions = [
            // Additional options, see http://docs.guzzlephp.org/en/latest/request-options.html
            'allow_redirects' => true,
            'cookies' => false
        ];
        // Return a PSR-7 compliant response object
        $response = $requestFactory->request($url, 'GET', $additionalOptions);
        // Get the content as a string on a successful request
        if ($response->getStatusCode() === 200) {
            $content = $response->getBody()->getContents();
        } else {
            $errorResponse = $response->getBody()->getContents();
            $errors = json_decode($errorResponse, true);
            if (is_array($errors) && is_array($errors['error']) && isset($errors['error']['message'])) {
                throw new \RuntimeException($errors['error']['message']);
            } else {
                throw new \RuntimeException($errorResponse);
            }
        }

        return $content;
    }

    /**
     * Get locale of current user
     *
     * @return string
     */
    protected function getLocale()
    {
        return $GLOBALS['BE_USER']->uc['lang'] ? $GLOBALS['BE_USER']->uc['lang'] : 'en';
    }

    /**
     * @param string $identifier
     * @return string|NULL
     */
    protected function getFromCache($identifier)
    {
        return $this->cache->get($identifier);
    }

    /**
     * @param string $identifier
     * @param string $content
     * @return void
     */
    protected function setToCache($identifier, $content)
    {
        $this->cache->set($identifier, $content);
    }
}
