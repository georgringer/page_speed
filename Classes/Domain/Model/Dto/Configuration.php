<?php

namespace GeorgRinger\PageSpeed\Domain\Model\Dto;

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

class Configuration
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
        if (is_array($configuration)) {
            $this->key = $configuration['key'];
            $this->demo = (bool)$configuration['demo'];
            $this->cacheTime = (int)$configuration['cacheTime'];
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if ($this->demo) {
            return true;
        }
        return (!empty($this->key)) ? true : false;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function isDemo()
    {
        return $this->demo;
    }

    /**
     * @return int
     */
    public function getCacheTime()
    {
        return $this->cacheTime;
    }
}
