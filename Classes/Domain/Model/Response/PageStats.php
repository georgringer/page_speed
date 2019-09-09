<?php
declare(strict_types=1);

namespace GeorgRinger\PageSpeed\Domain\Model\Response;

class PageStats
{

    public function __construct(array $in = null)
    {
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
    public function getNumberResources()
    {
        return $this->numberResources;
    }

    /**
     * @return int
     */
    public function getNumberHosts()
    {
        return $this->numberHosts;
    }

    /**
     * @return int
     */
    public function getNumberStaticResources()
    {
        return $this->numberStaticResources;
    }

    /**
     * @return int
     */
    public function getNumberJsResources()
    {
        return $this->numberJsResources;
    }

    /**
     * @return int
     */
    public function getNumberCssResources()
    {
        return $this->numberCssResources;
    }

    /**
     * @return int
     */
    public function getTotalRequestBytes()
    {
        return $this->totalRequestBytes;
    }

    /**
     * @return int
     */
    public function getHtmlResponseBytes()
    {
        return $this->htmlResponseBytes;
    }

    /**
     * @return int
     */
    public function getCssResponseBytes()
    {
        return $this->cssResponseBytes;
    }

    /**
     * @return int
     */
    public function getImageResponseBytes()
    {
        return $this->imageResponseBytes;
    }

    /**
     * @return int
     */
    public function getJavascriptResponseBytes()
    {
        return $this->javascriptResponseBytes;
    }

    /**
     * @return int
     */
    public function getOtherResponseBytes()
    {
        return $this->otherResponseBytes;
    }

    /**
     * @return int
     */
    public function getTotalRequestKb()
    {
        return $this->inKb($this->totalRequestBytes);
    }

    /**
     * @return int
     */
    public function getHtmlResponseKb()
    {
        return $this->inKb($this->htmlResponseBytes);
    }

    /**
     * @return int
     */
    public function getCssResponseKb()
    {
        return $this->inKb($this->cssResponseBytes);
    }

    /**
     * @return int
     */
    public function getImageResponseKb()
    {
        return $this->inKb($this->imageResponseBytes);
    }

    /**
     * @return int
     */
    public function getJavascriptResponseKb()
    {
        return $this->inKb($this->javascriptResponseBytes);
    }

    /**
     * @return int
     */
    public function getOtherResponseKb()
    {
        return $this->inKb($this->otherResponseBytes);
    }

    protected function inKb($value)
    {
        $value = $value / 1024;
        return round($value, 2);
    }
}
