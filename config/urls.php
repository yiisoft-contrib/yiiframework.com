<?php

return [
    '' => 'site/index',
    'guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>' => 'guide/index',
    'guide/<version:\\d\\.\\d>/<language:[\\w\\-]+>/<section:[\\w\\-]+>' => 'guide/view',
];
