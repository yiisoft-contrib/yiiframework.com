<?php

namespace app\commands;

use RuntimeException;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class TestController extends Controller
{
    public function actionEmail($to, $from): int
    {
        Yii::$app->mailer->compose()
            ->setTo($to)
            ->setFrom([$from => $from])
            ->setSubject('Email test from yiiframework.com')
            ->setTextBody('This is a test email sent from test/email command.')
            ->send();

        return ExitCode::OK;
    }

    public function actionError(): int
    {
        throw new RuntimeException('This is error test');
    }
}
