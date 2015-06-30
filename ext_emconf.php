<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Check your site with Google PageSpeed Insights',
	'description' => 'Performance & Usability are important to any site. Check every page for it and know how the site can be improved.',
	'category' => 'backend',
	'author' => 'Georg Ringer',
	'author_email' => 'typo3@ringerge.org',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 1,
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '7.2.0-7.9.99',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
);