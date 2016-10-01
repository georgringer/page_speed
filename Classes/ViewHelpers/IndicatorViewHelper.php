<?php

namespace GeorgRinger\PageSpeed\ViewHelpers;

use GeorgRinger\PageSpeed\Domain\Model\Response;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class IndicatorViewHelper extends AbstractViewHelper
{

    /**
     * @param int $number
     * @return string
     */
    public function render($number)
    {
        $number = (int)$number;
        if ($number === 0) {
            return 'none';
        } elseif ($number <= Response::INDICATOR_LOW) {
            return 'danger';
        } elseif ($number > Response::INDICATOR_HIGH) {
            return 'ok';
        } else {
            return 'warning';
        }
    }
}
