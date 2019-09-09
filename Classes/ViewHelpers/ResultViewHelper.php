<?php

namespace GeorgRinger\PageSpeed\ViewHelpers;

use GeorgRinger\PageSpeed\Domain\Model\Response\Result;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ResultViewHelper extends AbstractViewHelper
{

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('result', Result::class, 'Result', false, null);
        $this->registerArgument('hsc', 'bool', 'HSC', false, true);
    }

    public function render()
    {
        $result = $this->arguments['result'];
        $hsc = $this->arguments['hsc'];

        if ($result === null) {
            return '';
        }
        $searchReplace = [];
        foreach ($result->getArguments() as $argument) {
            $value = $hsc ? htmlspecialchars($argument['value']) : $argument['value'];
            switch ($argument['type']) {
                case 'HYPERLINK':
                    $searchReplace['{{BEGIN_LINK}}'] = sprintf('<a class="link" href="%s">', $value);
                    $searchReplace['{{END_LINK}}'] = '</a>';
                    break;
                case 'DURATION':
                    $searchReplace['{{' . $argument['key'] . '}}'] = '<code>' . $value . '</code>';
                    break;

                default:
                    $searchReplace['{{' . $argument['key'] . '}}'] = '<strong>' . $value . '</strong>';
            }
        }

        $search = array_keys($searchReplace);
        $replace = array_values($searchReplace);

        return str_replace($search, $replace, $result->getFormat());
    }
}
