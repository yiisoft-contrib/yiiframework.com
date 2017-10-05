<?php
return [
    'class' => yii\db\Connection::class,
    'dsn' => 'mysql:host=localhost;dbname=yiiframework_tests',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'on afterOpen' => function($event) {
        /** @var $db \yii\db\Connection */
        $db = $event->sender;
        $db->createCommand("SET time_zone = '+00:00';")->execute();
    },
];