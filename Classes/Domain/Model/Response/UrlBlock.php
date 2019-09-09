<?php
declare(strict_types=1);

namespace GeorgRinger\PageSpeed\Domain\Model\Response;

class UrlBlock
{

    /** @var Rule */
    protected $header;

    /** @var Result[] */
    protected $urls = [];

    public function __construct(array $raw)
    {
        $this->header = new Result($raw['header']);

        if (isset($raw['urls']) && is_array($raw['urls'])) {
            foreach ($raw['urls'] as $item) {
                $this->urls[] = new Result($item['result']);
            }
        }
    }

    public function getHeader(): Result
    {
        return $this->header;
    }

    public function getUrls(): array
    {
        return $this->urls;
    }
}
