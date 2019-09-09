<?php

namespace GeorgRinger\PageSpeed\ViewHelpers\Chart;

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

use GeorgRinger\PageSpeed\Domain\Model\Response;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class AssetsViewHelper extends AbstractViewHelper
{

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('id', 'string', 'ID');
        $this->registerArgument('result', 'array', 'Result');
    }

    public function render()
    {
        return '<script type="text/javascript">
				var chartResourcesBalloonText = "' . $this->translate('chart.resources.balloonText') . '";
				// <![CDATA[
				AmCharts.ready(function () {
					AmCharts.makeChart("' . $this->arguments['id'] . '", {
						type: "serial",
						"theme": "light",
						"dataProvider": ' . json_encode($this->getData($this->arguments['result'])) . ',
						categoryField: "type",
						rotate: true,
						"legend": {
							"horizontalGap": 10,
							"maxColumns": 1,
							"position": "right",
							"useGraphSettings": true,
							"markerdesktop": 15
						},
						categoryAxis: {
							gridPosition: "start",
							axisColor: "#DADADA"
						},
						valueAxes: [{
							axisAlpha: 0.2
						}],
						graphs: [
							{
								type: "column",
								title: "Desktop",
								valueField: "desktop",
								lineAlpha: 0,
								fillColors: "#ADD981",
								fillAlphas: 0.8,
								balloonText: chartResourcesBalloonText
							},
							{
								type: "column",
								title: "Mobile",
								valueField: "mobile",
								lineAlpha: 0,
								fillColors: "#FF9933",
								fillAlphas: 0.8,
								balloonText: chartResourcesBalloonText
							}]
					});
				});
				// ]]>
			</script>';
    }

    /**
     * @param array $result
     * @return array
     */
    protected function getData(array $result)
    {
        /** @var Response $mobile */
        $mobile = $result['mobile'];
        /** @var Response $desktop */
        $desktop = $result['desktop'];

        $data = [
            [
                'type' => $this->translate('resources.html'),
                'desktop' => $desktop->getPageStats()->getHtmlResponseKb(),
                'mobile' => $mobile->getPageStats()->getHtmlResponseKb(),
            ], [
                'type' => $this->translate('resources.css'),
                'desktop' => $desktop->getPageStats()->getCssResponseKb(),
                'mobile' => $mobile->getPageStats()->getCssResponseKb(),
            ],
            [
                'type' => $this->translate('resources.js'),
                'desktop' => $desktop->getPageStats()->getJavascriptResponseKb(),
                'mobile' => $mobile->getPageStats()->getJavascriptResponseKb(),
            ],
            [
                'type' => $this->translate('resources.img'),
                'desktop' => $desktop->getPageStats()->getImageResponseKb(),
                'mobile' => $mobile->getPageStats()->getImageResponseKb(),
            ],
            [
                'type' => $this->translate('resources.other'),
                'desktop' => $desktop->getPageStats()->getOtherResponseKb(),
                'mobile' => $mobile->getPageStats()->getOtherResponseKb(),
            ],
        ];

        return $data;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function translate($key)
    {
        return LocalizationUtility::translate($key, 'page_speed');
    }
}
