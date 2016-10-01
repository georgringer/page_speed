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

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
