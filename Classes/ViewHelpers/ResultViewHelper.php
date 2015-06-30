<?php

namespace GeorgRinger\PageSpeed\ViewHelpers;

use GeorgRinger\PageSpeed\Domain\Model\Response\Result;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class ResultViewHelper extends AbstractViewHelper {

	/**
	 * @param Result $result
	 * @param boolean $hsc
	 * @return string
	 */
	public function render(Result $result = NULL, $hsc = TRUE) {
		if (is_null($result)) {
			return '';
		}
		$searchReplace = array();
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