<?php
declare(strict_types=1);

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

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getPageRect(): array
    {
        return $this->pageRect;
    }
}
