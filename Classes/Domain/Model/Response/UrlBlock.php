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

class UrlBlock
{

    /** @var Rule */
    protected $header;

    /** @var array<Result> */
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

    /**
     * @return Rule
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return array
     */
    public function getUrls()
    {
        return $this->urls;
    }
}
