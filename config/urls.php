<?php

return [
    '' => 'site/index',
    'guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>' => 'guide/index',
    'guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>/<section:[a-z0-9\\.\\-]+>' => 'guide/view',
    'guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>/images/<image>' => 'guide/image',
];
