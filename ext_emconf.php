<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Check your site with Google PageSpeed Insights',
    'description' => 'Performance & Usability are important to any site. Check every page for it and know how the site can be improved.',
    'category' => 'backend',
    'author' => 'Georg Ringer',
    'author_email' => 'georg.ringer@gmail.com',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.4.0-9.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
