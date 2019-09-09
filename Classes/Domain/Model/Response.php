<?php
declare(strict_types=1);

namespace GeorgRinger\PageSpeed\Domain\Model;

use GeorgRinger\PageSpeed\Domain\Model\Response\PageStats;
use GeorgRinger\PageSpeed\Domain\Model\Response\Rule;
use GeorgRinger\PageSpeed\Domain\Model\Response\Screenshot;
use Symfony\Component\Process\Exception\RuntimeException;

class Response
{

    const GROUP_SPEED = 'SPEED';
    const GROUP_USABILITY = 'USABILITY';

    const LEVEL_WARNING_TRESHOLD = 10;

    const LEVEL_OK = 'OK';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_DANGER = 'DANGER';
    const LEVEL_NONOK = 'NONOK';

    const INDICATOR_LOW = 65;
    const INDICATOR_HIGH = 85;

    public function __construct($json)
    {
        $raw = json_decode($json, true);
        $this->id = $raw['id'];
        $this->kind = $raw['kind'];
        $this->responseCode = (int)$raw['responseCode'];
        $this->title = $raw['title'];
        $this->scoreSpeed = (int)$raw['ruleGroups']['SPEED']['score'];
        $this->scoreUsability = (int)$raw['ruleGroups']['USABILITY']['score'];
        $this->pageStats = new PageStats($raw['pageStats']);
        $this->version = sprintf('%s.%s', $raw['version']['major'], $raw['version']['minor']);
        $this->screenshot = new Screenshot($raw['screenshot']);
        foreach ($raw['formattedResults']['ruleResults'] as $key => $rule) {
            $this->rules[$key] = new Rule($rule);
        }
    }

    /** @var array */
    protected $rules;

    /** @var string */
    protected $version;

    /** @var string */
    protected $kind;

    /** @var string */
    protected $id;

    /** @var int */
    protected $responseCode;

    /** @var string */
    protected $title;

    /** @var int */
    protected $scoreSpeed;

    /** @var int */
    protected $scoreUsability;

    /** @var PageStats */
    protected $pageStats;

    /** @var Screenshot */
    protected $screenshot;

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getScoreSpeed()
    {
        return $this->scoreSpeed;
    }

    /**
     * @return int
     */
    public function getScoreUsability()
    {
        return $this->scoreUsability;
    }

    /**
     * @return PageStats
     */
    public function getPageStats()
    {
        return $this->pageStats;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return Screenshot
     */
    public function getScreenshot()
    {
        return $this->screenshot;
    }

    public function getRulesPassed()
    {
        return $this->filterRulesByImpact(true);
    }

    public function getRulesForUsability()
    {
        return $this->filterRulesByType($this->rules, self::GROUP_USABILITY);
    }

    public function getRulesForSpeed()
    {
        return $this->filterRulesByType($this->rules, self::GROUP_SPEED);
    }

    public function getRulesFailed()
    {
        return $this->filterRulesByImpact(self::LEVEL_NONOK);
    }

    public function getRulesDangerForUsability()
    {
        $rules = $this->filterRulesByImpact(self::LEVEL_DANGER);
        return $this->filterRulesByType($rules, self::GROUP_USABILITY);
    }

    public function getRulesWarningForUsability()
    {
        $rules = $this->filterRulesByImpact(self::LEVEL_WARNING);
        return $this->filterRulesByType($rules, self::GROUP_USABILITY);
    }

    public function getRulesPassedForUsability()
    {
        $rules = $this->filterRulesByImpact(self::LEVEL_OK);
        return $this->filterRulesByType($rules, self::GROUP_USABILITY);
    }

    public function getRulesDangerForSpeed()
    {
        $rules = $this->filterRulesByImpact(self::LEVEL_DANGER);
        return $this->filterRulesByType($rules, self::GROUP_SPEED);
    }

    public function getRulesWarningForSpeed()
    {
        $rules = $this->filterRulesByImpact(self::LEVEL_WARNING);
        return $this->filterRulesByType($rules, self::GROUP_SPEED);
    }

    public function getRulesPassedForSpeed()
    {
        $rules = $this->filterRulesByImpact(self::LEVEL_OK);
        return $this->filterRulesByType($rules, self::GROUP_SPEED);
    }

    protected function filterRulesByImpact($level)
    {
        $rules = [];
        foreach ($this->rules as $rule) {
            /** @var $rule Rule */
            switch ($level) {
                case self::LEVEL_OK:
                    if ($rule->getRuleImpact() == 0) {
                        $rules[] = $rule;
                    }
                    break;
                case self::LEVEL_WARNING:
                    if ($rule->getRuleImpact() > 0 && $rule->getRuleImpact() < self::LEVEL_WARNING_TRESHOLD) {
                        $rules[] = $rule;
                    }
                    break;
                case self::LEVEL_DANGER:
                    if ($rule->getRuleImpact() >= self::LEVEL_WARNING_TRESHOLD) {
                        $rules[] = $rule;
                    }
                    break;
                case self::LEVEL_NONOK:
                    if ($rule->getRuleImpact() > 0) {
                        $rules[] = $rule;
                    }
                    break;
                default:
                    throw new RuntimeException(sprintf('The level "%s" is not supported', $level));
            }
        }
        return $rules;
    }

    protected function filterRulesByType(array $rules, $type)
    {
        $filtered = [];
        foreach ($rules as $rule) {
            /** @var $rule Rule */
            if ($rule->getGroup() === $type) {
                $filtered[] = $rule;
            }
        }
        return $filtered;
    }
}
