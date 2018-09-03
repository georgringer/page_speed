<?php

namespace GeorgRinger\PageSpeed\Domain\Model\Dto;

use TYPO3\CMS\Core\SingletonInterface;

class Configuration implements SingletonInterface
{

    /** @var string */
    protected $key = '';

    /** @var bool */
    protected $demo = true;

    /** @var int */
    protected $cacheTime = 0;

    public function __construct()
    {
        $configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['page_speed']);
        if (\is_array($configuration)) {
            $this->key = (string)$configuration['key'];
            $this->demo = (bool)$configuration['demo'];
            $this->cacheTime = (int)$configuration['cacheTime'];
        }
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->demo) {
            return true;
        }
        return !empty($this->key);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function isDemo(): bool
    {
        return $this->demo;
    }

    /**
     * @return int
     */
    public function getCacheTime(): int
    {
        return $this->cacheTime;
    }
}
