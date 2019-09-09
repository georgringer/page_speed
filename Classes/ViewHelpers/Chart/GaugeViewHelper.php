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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class GaugeViewHelper extends AbstractViewHelper
{

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @param string $id
     * @param Response $response
     * @return string
     */
    public function render($id, Response $response)
    {
        $arrows = [];
        if ($response->getScoreSpeed() > 0) {
            $arrows[] = '{
							"color": "blue",
							"innerRadius": "10%",
							"nailRadius": 0,
							"value": ' . $response->getScoreSpeed() . ',
							"title": "Speed",
							"radius": "100%"
						}';
        }
        if ($response->getScoreUsability() > 0) {
            $arrows[] = '{
							"color": "redo",
							"title": "Usability",
							"innerRadius": "0%",
							"nailRadius": 0,
							"value": ' . $response->getScoreUsability() . ',
							"radius": "100%"
						}';
        }
        return '<script type="text/javascript">
					// <![CDATA[
					AmCharts.ready(function () {
						AmCharts.makeChart("' . $id . '", {
							"theme": "light",
							"type": "gauge",
							"legend": {
								"horizontalGap": 0,
								"maxColumns": 2,
								"position": "left",
								"useGraphSettings": false,
								"markerSize": 5
							},
							"axes": [{
								"bands": [
									{
										"color": "#FD9987",
										"startValue": 0,
										"endValue": 65
									},
									{
										"color": "#FDD187",
										"startValue": 65,
										"endValue": 90
									},
									{
										"color": "#98F15F",
										"startValue": 90,
										"endValue": 100
									}
								],
								"axisColor": "#ccc",
								"axisThickness": 0,
								"endValue": 100,
								"gridInside": true,
								"inside": true,
								"radius": "100%",
								"valueInterval": 100,
								"tickColor": "#67b7dc"
							}],

							"arrows": [' . implode(',', $arrows) . ']
						});
					});
					// ]]>
				</script>';
    }
}
