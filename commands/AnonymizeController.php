<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Expression;

/**
 * Anonymize database so dump can be safely passed around.
 */
class AnonymizeController extends Controller
{
    public function actionDatabase(): int
    {
        $dsn = Yii::$app->params['components.db']['dsn'];

        if (!$this->confirm("Anonymize database \"$dsn\"?")) {
            return ExitCode::OK;
        }

        $passwordHash = Yii::$app->security->generatePasswordHash('password');
        $authKey = Yii::$app->security->generateRandomString();

        Yii::$app->db->createCommand()->update(
            'user',
            [
                'password_hash' => $passwordHash,
                'auth_key' => $authKey,
                'email' => new Expression('CONCAT(id, \'@example.com\')'),
                'password_reset_token' => null,
                'email_verification_token' => null,
            ]
        )->execute();

        Yii::$app->db->createCommand()->update('auth', [
            'source_email' => new Expression('CONCAT(user_id, \'@example.com\')'),
        ])->execute();

        $this->stdout("\n");
        return ExitCode::OK;
    }
}
