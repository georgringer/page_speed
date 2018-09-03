<?php
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

namespace GeorgRinger\PageSpeed\Domain\Model\Response;

class Screenshot
{

    /** @var string */
    protected $mimeType;

    /** @var string */
    protected $data;

    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /** @var array */
    protected $pageRect;

    public function __construct(array $raw = null)
    {
        if (\is_array($raw)) {
            $this->mimeType = $raw['mime_type'];
            $this->data = str_replace(['_', '-'], ['/', '+'], $raw['data']);
            $this->width = (int)$raw['width'];
            $this->height = (int)$raw['height'];
            $this->pageRect = $raw['page_rect'];
        }
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return array
     */
    public function getPageRect()
    {
        return $this->pageRect;
    }
}
