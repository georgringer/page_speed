<?php

namespace GeorgRinger\PageSpeed\Domain\Repository;

use GeorgRinger\PageSpeed\Domain\Model\Dto\Configuration;
use GeorgRinger\PageSpeed\Domain\Model\Response;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\HttpRequest;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageSpeedRepository
{

    /** @var FrontendInterface */
    protected $cache;

    /** @var Configuration */
    protected $configuration;

    /** @var array */
    public const STRATEGIES = ['desktop', 'mobile'];

    public function __construct()
    {
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('page_speed_response');
        $this->configuration = GeneralUtility::makeInstance(Configuration::class);
    }

    /**
     * @param string $identifier
     * @return Response[]
     */
    public function findByIdentifier(string $identifier, bool $forceRequestToApi = true): array
    {
        $apiResponses = $result = [];

        $locale = $this->getLocale();
        foreach (self::STRATEGIES as $strategy) {
            if ($this->configuration->isDemo()) {
                $path = ExtensionManagementUtility::extPath('page_speed', 'Resources/Private/Examples/');
                $apiResponses[$strategy] = GeneralUtility::getUrl($path . $strategy . '.json');
            } else {
                $cacheIdentifier = $this->getCacheIdentifier($identifier, $strategy);

                $resultFromApi = $this->getFromCache($cacheIdentifier);
                if (!$resultFromApi && $forceRequestToApi) {
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
     * @todo what to do with the locale
     */
    public function clearByIdentifier($identifier): void
    {
        foreach (self::STRATEGIES as $strategy) {
            $cacheIdentifier = $this->getCacheIdentifier($identifier, $strategy);
            $this->cache->remove($cacheIdentifier);
        }
    }

    /**
     * @param string $url
     * @param string $strategy
     * @return string
     */
    protected function getCacheIdentifier(string $url, string $strategy): string
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
    protected function getResponseFromApi(string $identifier, string $strategy, string $locale = 'en'): string
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
    protected function apiCall($url): string
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $additionalOptions = [
            'allow_redirects' => true,
            'cookies' => false,
            'force_ip_resolve' => 'v4'
        ];

        $response = $requestFactory->request($url, 'GET', $additionalOptions);
        if ($response->getStatusCode() === 200) {
            $content = $response->getBody()->getContents();
        } else {
            $errorResponse = $response->getBody()->getContents();
            $errors = json_decode($errorResponse, true);
            if (is_array($errors) && is_array($errors['error']) && isset($errors['error']['message'])) {
                throw new \RuntimeException($errors['error']['message']);
            }

            throw new \RuntimeException($errorResponse);
        }

        return $content;
    }

    /**
     * Get locale of current user
     *
     * @return string
     */
    protected function getLocale(): string
    {
        return $GLOBALS['BE_USER']->uc['lang'] ?: 'en';
    }

    /**
     * @param string $identifier
     * @return string|NULL
     */
    protected function getFromCache($identifier): ?string
    {
        return $this->cache->get($identifier);
    }

    /**
     * @param string $identifier
     * @param string $content
     * @return void
     */
    protected function setToCache(string $identifier, string $content): void
    {
        $this->cache->set($identifier, $content);
    }
}
