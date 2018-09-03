<?php

namespace GeorgRinger\PageSpeed\ViewHelpers;

use GeorgRinger\PageSpeed\Domain\Model\Response;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class IndicatorRulesViewHelper extends AbstractViewHelper
{

    public function initializeArguments()
    {
        $this->registerArgument('number', 'int', 'number');
    }

    public function render()
    {
        $number = (int)$this->arguments['number'];
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
