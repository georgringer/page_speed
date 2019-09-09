<?php
declare(strict_types=1);

namespace GeorgRinger\PageSpeed\Domain\Model\Response;

class Rule
{

    /** @var Result */
    protected $summary;

    /** @var string */
    protected $localizedRuleName;

    /** @var float */
    protected $ruleImpact;

    /** @var string */
    protected $group;

    /** @var array */
    protected $urlBlocks = [];

    public function __construct(array $raw)
    {
        $this->localizedRuleName = $raw['localizedRuleName'];
        $this->ruleImpact = $raw['ruleImpact'];
        $this->group = $raw['groups'][0];

        if (isset($raw['summary']) && is_array($raw['summary'])) {
            $this->summary = new Result($raw['summary']);
        }
        if (isset($raw['urlBlocks']) && is_array($raw['urlBlocks'])) {
            foreach ($raw['urlBlocks'] as $item) {
                $this->urlBlocks[] = new UrlBlock($item);
            }
        }
    }

    public function getSummary(): Result
    {
        return $this->summary;
    }

    public function getLocalizedRuleName(): string
    {
        return $this->localizedRuleName;
    }

    public function getRuleImpact(): float
    {
        return $this->ruleImpact;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getHasImpact(): string
    {
        return (float)$this->ruleImpact > 0 ? 'danger' : 'success';
    }

    public function getUrlBlocks(): array
    {
        return $this->urlBlocks;
    }
}
