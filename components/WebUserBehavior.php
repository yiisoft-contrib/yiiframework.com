<?php
/**
 * Created by PhpStorm.
 * User: cebe
 * Date: 25.07.17
 * Time: 02:46
 */

namespace app\components;


use Yii;
use yii\base\Behavior;
use yii\db\Expression;
use yii\web\User;

class WebUserBehavior extends Behavior
{
    public function events()
    {
        return [
            User::EVENT_AFTER_LOGIN => 'afterLogin',
        ];
    }

    public function afterLogin($event)
    {
        /** @var $user User */
        $user = $event->sender;
        $user->identity->updateAttributes([
            'login_time' => new Expression('NOW()'),
            // TODO this should probably go into DB session storage to list all active sessions
            'login_ip' => Yii::$app->request->userIP,
            'login_attempts' => 0,
        ]);
    }
}
