<?php
$content_1 = <<<EOD1
Composer content here
EOD1;
$content_2 = <<<EOD2
Template content here
EOD2;
$content_3 = <<<EOD3
Welcome content here
EOD3;
$content_4 = <<<EOD4
Migrations content here
EOD4;
$content_5 = <<<EOD5
Gii content here
EOD5;

return [
    [
        'title' => 'Composer',
        'sub_title' => 'Step 1',
        'image' => '@web/image/tour/composer.png',
        'content' => $content_1,
    ],
    [
        'title' => 'Basic project template',
        'sub_title' => 'Step 2',
        'image' => '@web/image/tour/project-install.png',
        'content' => $content_2,
    ],
    [
        'title' => 'Welcome page',
        'sub_title' => 'Step 3',
        'image' => '@web/image/tour/start-app-installed.png',
        'content' => $content_3,
    ],
    [
        'title' => 'Migrations',
        'sub_title' => 'Step 4',
        'image' => '@web/image/tour/migration.png',
        'content' => $content_4,
    ],
    [
        'title' => 'Gii - Yii Code Generator',
        'sub_title' => 'Step 5',
        'image' => '@web/image/tour/gii.png',
        'content' => $content_5,
    ],
];
