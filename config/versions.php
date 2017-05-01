<?php

return [
	// version numbers for which API documentation and Guide are available
	// the first item must be the latest version
	'api' => [
		'2.0',
		'1.1',
		'1.0',
	],

	// detailed information about current major versions e.g. for display on the download page
	'version-info' => [
		'2.0' => [
			'status' => 'stable',
			// TODO doc download
			// - guide pdf
			// - blog pdf
			// - api html
			'min-php-version' => '5.4.0',
			'support-until' => false,
			'security-until' => false,
			'github-url' => 'https://github.com/yiisoft/yii2',
			'git-url' => 'git@github.com:yiisoft/yii2.git',
			'svn-url' => 'https://github.com/yiisoft/yii2/trunk/',
			'summary' => <<<HTML
				Yii 2.0 is a complete rewrite of Yii on top of PHP 5.4.0.
				It is aimed to be a state-of-the-art of the new generation of PHP framework.
				Yii 2.0 is not compatible with version 1.1.
HTML
		],
		'1.1' => [
			'status' => 'maintenance',
			// TODO doc download
			// - guide pdf
			// - blog pdf
			// - api html
			'download-url'=>'https://github.com/yiisoft/yii/releases/download/1.1.18/yii-1.1.18.018a89',
			'min-php-version' => '5.1.0',
			'support-until' => 'December 31, 2016 ',
			'security-until' => 'December 31, 2019 ',
			'github-url' => 'https://github.com/yiisoft/yii',
			'git-url' => 'git@github.com:yiisoft/yii.git',
			'svn-url' => 'https://github.com/yiisoft/yii/trunk/',
			'summary' => <<<HTML
				Yii 1.1 is currently under <a href="http://www.yiiframework.com/news/90/update-on-yii-1-1-support-and-end-of-life/">maintenance mode</a>.
		        Continued support and bug fixes for this version have been provided until December 31, 2016.
		        Security fixes will be provided until at least December 31, 2019 if necessary.
HTML
		],
		'1.0' => [
			'status' => 'deprecated',
			// TODO doc download
			// - guide pdf
			// - blog pdf
			// - api html
			'min-php-version' => '5.1.0',
			'support-until' => 'not supported anymore!',
			'security-until' => 'not supported anymore!',
			'github-url' => 'https://github.com/yiisoft/yii',
			'git-url' => 'git@github.com:yiisoft/yii.git',
			'svn-url' => 'https://github.com/yiisoft/yii/trunk/',
			'summary' => <<<HTML
				Yii 1.0 is the first version of Yii Framework. It is superseeded by Yii 1.1 and <strong>not supported anymore</strong>.
HTML
		],
	],
	'minor-versions' => [
		'2.0' => [
			'2.0.11' => 'February 1, 2017',
			'2.0.10' => 'October 20, 2016',
			'2.0.9' => 'July 11, 2016',
			'2.0.8' => 'April 28, 2016',
			'2.0.7' => 'February 14, 2016',
			'2.0.6' => 'August 05, 2015',
			'2.0.5' => 'July 11, 2015',
			'2.0.4' => 'May 10, 2015',
			'2.0.3' => 'March 01, 2015',
			'2.0.2' => 'January 11, 2015',
			'2.0.1' => 'December 07, 2014',
			'2.0.0' => 'October 12, 2014',
			'2.0.0-rc' => 'September 27, 2014',
			'2.0.0-beta' => 'April 13, 2014',
			'2.0.0-alpha' => 'December 1, 2013',
		],
		'1.1' => [
			'1.1.18' => 'April 19, 2017',
			'1.1.17' => 'January 13, 2016',
			'1.1.16' => 'December 21, 2014',
			'1.1.15' => 'June 29, 2014',
			'1.1.14' => 'August 11, 2013',
			'1.1.13' => 'December 30, 2012',
			'1.1.12' => 'August 19, 2012',
			'1.1.11' => 'July 29, 2012',
			'1.1.10' => 'February 12, 2012',
			'1.1.9' => 'January 1, 2012',
			'1.1.8' => 'June 26, 2011',
			'1.1.7' => 'March 27, 2011',
			'1.1.6' => 'January 16, 2011',
			'1.1.5' => 'November 14, 2010',
			'1.1.4' => 'September 5, 2010',
			'1.1.3' => 'July 4, 2010',
			'1.1.2' => 'May 2, 2010',
			'1.1.1' => 'March 14, 2010',
			'1.1.0' => 'January 10, 2010',
			'1.1rc' => 'December 13, 2009',
			'1.1b' => 'November 1, 2009',
			'1.1a' => 'October 1, 2009',
		],
		'1.0' => [
			'1.0.12' => 'March 14, 2010',
			'1.0.11' => 'December 13, 2009',
			'1.0.10' => 'October 18, 2009',
			'1.0.9' => 'September 6, 2009',
			'1.0.8' => 'August 9, 2009',
			'1.0.7' => 'July 5, 2009',
			'1.0.6' => 'June 7, 2009',
			'1.0.5' => 'May 10, 2009',
			'1.0.4' => 'April 5, 2009',
			'1.0.3' => 'March 1, 2009',
			'1.0.2' => 'February 1, 2009',
			'1.0.1' => 'January 4, 2009',
			'1.0' => 'December 3, 2008',
			'1.0rc' => 'November 2008',
			'1.0b' => 'October 2008',
			'1.0a' => 'October 2008',
		],
	],
	/*
	'yii1.revisions' => [

		'1.1.17' => '',
		'1.1.16' => '',
		'1.1.15' => '',
		'1.1.14' => '',
		'1.1.13' => '',
		'1.1.12' => '',
		'1.1.11' => '',
		'1.1.10' => '',
		'1.1.9' => '',
		'1.1.8' => '',
		'1.1.7' => '',
		'1.1.6' => '',
		'1.1.5' => '',
		'1.1.4' => '',
		'1.1.3' => '',
		'1.1.2' => '',
		'1.1.1' => 'r1907',
		'1.1.0' => 'r1700',
		'1.1rc' => 'r1585',
		'1.1b' => 'r1504',
		'1.1a' => 'r1436', // docs are r 1435

		'1.0.12' => 'r1898',
		'1.0.11' => 'r1579',
		'1.0.10' => 'r1472',
		'1.0.9' => 'r1396',
		'1.0.8' => 'r1317',
		'1.0.7' => 'r1212',
		'1.0.6' => 'r1102',
		'1.0.5' => 'r1018',
		'1.0.4' => 'r920',
		'1.0.3' => 'r780', // r775 also exists
		'1.0.2' => 'r614',
		'1.0.1' => 'r473',
		'1.0' => 'r322',
		'1.0rc' => 'r187',
		'1.0b' => 'r110',
		'1.0a' => 'r39',
	],*/
];
