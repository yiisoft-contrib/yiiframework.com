<?php

return [
    'books' => 'site/books',
    'contribute' => 'site/contribute',
    'chat' => 'site/chat',
    'contact' => 'site/contact',
    'captcha' => 'site/captcha',
    'about' => 'site/about',
    'license' => 'site/license',
    'news' => 'site/news',
    'team' => 'site/team',
    'report-issue' => 'site/report-issue',
    'security' => 'site/security',
    'download' => 'site/download',

    // TODO implement redirect for url/ to url, needed for old 1.1 api urls

    '' => 'site/index',
    'doc/api/<version:\\d\\.\\d>' => 'api/index',
    'doc/api/<version:\\d\\.\\d>/<section:.+>' => 'api/view',
    'doc/guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>' => 'guide/index',
    'doc/guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>/<section:[a-z0-9\\.\\-]+>' => 'guide/view',
    'doc/guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>/images/<image>' => 'guide/image',
    'doc/guide/<version:\\d\\.\\d>' => 'guide/entry',
    'doc/guide' => 'guide/entry',

    'search' => 'search/global',

    // urls from old site redirect to new location
    'doc-2.0/guide-<section:[A-z0-9\\.\\-]+>.html' => 'guide/redirect',
    'doc-2.0/<section:.+>.html' => 'api/redirect',
    '<url:doc/terms>' => 'site/redirect'


];
