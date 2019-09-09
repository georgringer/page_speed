<?php
declare(strict_types=1);

namespace GeorgRinger\PageSpeed\Domain\Model\Response;

class Result
{

    /** @var string */
    protected $format;

    /** @var array */
    protected $arguments = [];

    public function __construct(array $raw)
    {
        $this->format = $raw['format'];
        if (isset($raw['args'])) {
            $this->arguments = $raw['args'];
        }
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }
}
