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

    /**
     * @return Result
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getLocalizedRuleName()
    {
        return $this->localizedRuleName;
    }

    /**
     * @return float
     */
    public function getRuleImpact()
    {
        return $this->ruleImpact;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getHasImpact()
    {
        return (float)$this->ruleImpact > 0 ? 'danger' : 'success';
    }

    /**
     * @return array
     */
    public function getUrlBlocks()
    {
        return $this->urlBlocks;
    }
}
