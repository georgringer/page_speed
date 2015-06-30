<?php

namespace GeorgRinger\PageSpeed\Domain\Model\Response;

/**
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

class PageStats {

	public function __construct(array $in = NULL) {
		if (!empty($in)) {
			$this->numberResources = (int)$in['numberResources'];
			$this->numberHosts = (int)$in['numberHosts'];
			$this->numberStaticResources = (int)$in['numberStaticResources'];
			$this->numberJsResources = (int)$in['numberJsResources'];
			$this->numberCssResources = (int)$in['numberCssResources'];
			$this->totalRequestBytes = (int)$in['totalRequestBytes'];
			$this->htmlResponseBytes = (int)$in['htmlResponseBytes'];
			$this->cssResponseBytes = (int)$in['cssResponseBytes'];
			$this->imageResponseBytes = (int)$in['imageResponseBytes'];
			$this->javascriptResponseBytes = (int)$in['javascriptResponseBytes'];
			$this->otherResponseBytes = (int)$in['otherResponseBytes'];
		}
	}

	/** @var int */
	protected $numberResources;

	/** @var int */
	protected $numberHosts;

	/** @var int */
	protected $numberStaticResources;

	/** @var int */
	protected $numberJsResources;

	/** @var int */
	protected $numberCssResources;

	/** @var int */
	protected $totalRequestBytes;

	/** @var int */
	protected $htmlResponseBytes;

	/** @var int */
	protected $cssResponseBytes;

	/** @var int */
	protected $imageResponseBytes;

	/** @var int */
	protected $javascriptResponseBytes;

	/** @var int */
	protected $otherResponseBytes;

	/**
	 * @return int
	 */
	public function getNumberResources() {
		return $this->numberResources;
	}

	/**
	 * @return int
	 */
	public function getNumberHosts() {
		return $this->numberHosts;
	}

	/**
	 * @return int
	 */
	public function getNumberStaticResources() {
		return $this->numberStaticResources;
	}

	/**
	 * @return int
	 */
	public function getNumberJsResources() {
		return $this->numberJsResources;
	}

	/**
	 * @return int
	 */
	public function getNumberCssResources() {
		return $this->numberCssResources;
	}

	/**
	 * @return int
	 */
	public function getTotalRequestBytes() {
		return $this->totalRequestBytes;
	}

	/**
	 * @return int
	 */
	public function getHtmlResponseBytes() {
		return $this->htmlResponseBytes;
	}

	/**
	 * @return int
	 */
	public function getCssResponseBytes() {
		return $this->cssResponseBytes;
	}

	/**
	 * @return int
	 */
	public function getImageResponseBytes() {
		return $this->imageResponseBytes;
	}

	/**
	 * @return int
	 */
	public function getJavascriptResponseBytes() {
		return $this->javascriptResponseBytes;
	}

	/**
	 * @return int
	 */
	public function getOtherResponseBytes() {
		return $this->otherResponseBytes;
	}

	/**
	 * @return int
	 */
	public function getTotalRequestKb() {
		return $this->inKb($this->totalRequestBytes);
	}

	/**
	 * @return int
	 */
	public function getHtmlResponseKb() {
		return $this->inKb($this->htmlResponseBytes);
	}

	/**
	 * @return int
	 */
	public function getCssResponseKb() {
		return $this->inKb($this->cssResponseBytes);
	}

	/**
	 * @return int
	 */
	public function getImageResponseKb() {
		return $this->inKb($this->imageResponseBytes);
	}

	/**
	 * @return int
	 */
	public function getJavascriptResponseKb() {
		return $this->inKb($this->javascriptResponseBytes);
	}

	/**
	 * @return int
	 */
	public function getOtherResponseKb() {
		return $this->inKb($this->otherResponseBytes);
	}

	protected function inKb($value) {
		$value = $value / 1024;
		return round($value, 2);
	}

}