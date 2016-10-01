<?php

namespace GeorgRinger\PageSpeed\ViewHelpers;

use GeorgRinger\PageSpeed\Domain\Model\Response\Result;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class UrlBlockViewHelper extends AbstractViewHelper
{

    /**
     * Disable the escaping interceptor because otherwise the child nodes would be escaped before this view helper
     * can decode the text's entities.
     *
     * @var bool
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * @param Result $result
     * @param bool $hsc
     * @param string $strategy
     * @return string
     */
    public function render(Result $result = null, $hsc = true, $strategy = '')
    {
        if (is_null($result)) {
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

                case 'INT_LITERAL':
                    $searchReplace['{{' . $argument['key'] . '}}'] = '<strong>' . $value . '</strong>';
                    break;
                case 'SNAPSHOT_RECT':
                    $searchReplace['{{' . $argument['key'] . '}}'] = sprintf(
                        '<a data-strategy="%s" data-rects="%s" data-rects-secondary="%s" href="#" class="screenshot-toggle">siehe screen</a>',
                        $strategy,
                        $this->generateRectAttribute($argument['rects']),
                        $this->generateRectAttribute($argument['secondary_rects'])
                    );
                    break;
                case 'BYTES':
                case 'DISTANCE':
                case 'PERCENTAGE':
                case 'STRING_LITERAL':
                case 'URL':
                case 'VERBATIM_STRING':
                default:

                    $searchReplace['{{' . $argument['key'] . '}}'] = '<code>' . $value . '</code>';
            }
        }

        $search = array_keys($searchReplace);
        $replace = array_values($searchReplace);

        return str_replace($search, $replace, $result->getFormat());
    }

    /**
     * @param array $data
     * @return string
     */
    protected function generateRectAttribute(array $data = null)
    {
        if (!is_array($data)) {
            return '';
        }
        $tmp = [];
        foreach ($data as $item) {
            $val = implode(',', $item);
            $tmp[$val] = $val;
        }

        return implode(';', $tmp);
    }
}
