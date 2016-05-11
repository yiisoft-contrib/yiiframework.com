<?php

return [
    'books' => 'site/books',
    'contribute' => 'site/contribute',
    'chat' => 'site/chat',
    'contact' => 'site/contact',
    'captcha' => 'site/captcha',
    'license' => 'site/license',
    'team' => 'site/team',
    'wiki' => 'site/wiki',
    'report-issue' => 'site/report-issue',
    'security' => 'site/security',
    'download' => 'site/download',
    'tos' => 'site/tos',
    'logo' => 'site/logo',
    'tour' => 'site/tour',
    'resources' => 'site/resources',

	'news' => 'news/index',
	'news/<id:\d+>/<name>' => 'news/view',
	'news/<id:\d+>' => 'news/view',
	'news/<action:\w+>' => 'news/<action>',

    'download/<category:[\w-]+>/<file:[\w\d-.]+>' => 'site/file',

    'logout' => 'site/logout',
    'login' => 'site/login',
    'signup' => 'site/signup',
    'auth' => 'site/auth',

    // TODO implement redirect for url/ to url, needed for old 1.1 api urls

    '' => 'site/index',
    'doc/api' => 'api/entry',
    'doc/api/class-members' => 'api/class-members',
    'doc/api/<version:\\d\\.\\d>' => 'api/index',
    'doc/api/<version:\\d\\.\\d>/<section:.+>' => 'api/view',
    'doc/<type:guide|blog>/<version:\\d\\.\\d>/<language:[\\w\\-]+>' => 'guide/index',
    'doc/<type:guide|blog>/<version:\\d\\.\\d>/<language:[\\w\\-]+>/<section:[a-z0-9\\.\\-]+>' => 'guide/view',
    'doc/<type:guide|blog>/<version:\\d\\.\\d>/<language:[\\w\\-]+>/images/<image>' => 'guide/image',
    'doc/guide/<version:\\d\\.\\d>' => 'guide/entry',
    'doc/guide' => 'guide/entry',
    'doc/blog/<version:\\d\\.\\d>' => 'guide/blog-entry',
    'doc/blog' => 'guide/blog-entry',

    'doc/download/yii-guide-<version:\\d\\.\\d>-<language:[\\w\\-]+>.<format:pdf>' => 'guide/download',
    'doc/download/yii-docs-<version:\\d\\.\\d>-<language:[\\w\\-]+>.<format:tar\\.gz|tar\\.bz2>' => 'guide/download',

    'search' => 'search/global',
    'search/suggest' => 'search/suggest',
    'search/as-you-type' => 'search/as-you-type',
    'search/extension' => 'search/extension',

    // urls from old site redirect to new location
    'doc-2.0/guide-<section:[A-z0-9\\.\\-]+>.html' => 'guide/redirect',
    'doc-2.0/<section:.+>.html' => 'api/redirect',
    '<url:doc/terms>' => 'site/redirect',
    '<url:about|performance|demos|doc>' => 'site/redirect',

    'extensions/package/<vendorName:[\w\-\.]+>/<packageName:[\w\-\.]+>' => 'extension/package',
    'extensions/page/<page:\d+>' => 'extension/index',
    'extensions' => 'extension/index',
];
