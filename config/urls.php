<?php

return [

	// static pages
	'' => 'site/index',
    'books' => 'site/books',
    'contribute' => 'site/contribute',
    'chat' => 'site/chat',
    'contact' => 'site/contact',
    'captcha' => 'site/captcha',
    'license' => 'site/license',
    'team' => 'site/team',
    'report-issue' => 'site/report-issue',
    'security' => 'site/security',
    'download' => 'site/download',
    'tos' => 'site/tos',
    'logo' => 'site/logo',
    'tour' => 'site/tour',
    'resources' => 'site/resources',

	// file download
    'download/<category:[\w-]+>/<file:[\w\d-.]+>' => 'site/file',

	// news
	'news' => 'news/index',
	'news/<id:\d+>/<name>' => 'news/view',
	'news/<id:\d+>' => 'news/view',
	'news/<action:[\w-]+>' => 'news/<action>',

	// auth, login and logout
    'logout' => 'auth/logout',
    'login' => 'auth/login',
    'signup' => 'auth/signup',
    'auth/<action:[\w-]+>' => 'auth/<action>',

	// class api docs
    'doc/api' => 'api/entry',
    'doc/api/class-members' => 'api/class-members',
    'doc/api/<version:\\d\\.\\d>' => 'api/index',
    'doc/api/<version:\\d\\.\\d>/<section:.+>' => 'api/view',
	// definitive guide and Yii 1 blog tutorial
    'doc/<type:guide|blog>/<version:\\d\\.\\d>/<language:[\\w\\-]+>' => 'guide/index',
    'doc/<type:guide|blog>/<version:\\d\\.\\d>/<language:[\\w\\-]+>/<section:[a-z0-9\\.\\-]+>' => 'guide/view',
    'doc/<type:guide|blog>/<version:\\d\\.\\d>/<language:[\\w\\-]+>/images/<image>' => 'guide/image',
    'doc/guide/<version:\\d\\.\\d>' => 'guide/entry',
    'doc/guide' => 'guide/entry',
    'doc/blog/<version:\\d\\.\\d>' => 'guide/blog-entry',
    'doc/blog' => 'guide/blog-entry',

    'doc/download/yii-guide-<version:\\d\\.\\d>-<language:[\\w\\-]+>.<format:pdf>' => 'guide/download',
    'doc/download/yii-docs-<version:\\d\\.\\d>-<language:[\\w\\-]+>.<format:tar\\.gz|tar\\.bz2>' => 'guide/download',

	// search
    'search' => 'search/global',
    'search/suggest' => 'search/suggest',
    'search/as-you-type' => 'search/as-you-type',
    'search/extension' => 'search/extension',

	// extensions
	// TODO handle URLs from old site
    'extensions/package/<vendorName:[\w\-\.]+>/<packageName:[\w\-\.]+>' => 'extension/package',
    'extensions/page/<page:\d+>' => 'extension/index',
    'extensions' => 'extension/index',

	// wiki
	// TODO handle URLs from old site
	'wiki' => 'wiki/index',

	// user profiles and user ranking
	'user' => 'user/index',
	'user/<id:\d+>' => 'user/view',
	'user/<action:halloffame|profile>' => 'user/<action>',
	'badges' => 'user/badges',
	'badges/<name:[\w-]+>' => 'user/view-badge',

	// ajax actions for handling user interactions
	'ajax/<action:[\w-]+>' => 'ajax/<action>',

	// urls from old site redirect to new location
	'doc-2.0/guide-<section:[A-z0-9\\.\\-]+>.html' => 'guide/redirect',
	'doc-2.0/<section:.+>.html' => 'api/redirect',
	'<url:doc/terms>' => 'site/redirect',
	'<url:about|performance|demos|doc>' => 'site/redirect',


	// admin
	'admin/<controller:user>' => '<controller>-admin/index',
	'admin/<controller:user>/<action:[\w-]+>' => '<controller>-admin/<action>',

];
