<?php

return [
    '' => 'site/index',
    'api/<version:\\d\\.\\d>' => 'api/index',
    'api/<version:\\d\\.\\d>/<section>' => 'api/view',
    'guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>' => 'guide/index',
    'guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>/<section:[a-z0-9\\.\\-]+>' => 'guide/view',
    'guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>/images/<image>' => 'guide/image',
];
