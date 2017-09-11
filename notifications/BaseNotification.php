<?php
/**
 * Created by PhpStorm.
 * User: cebe
 * Date: 23.07.17
 * Time: 01:29
 */

namespace app\notifications;


use app\jobs\NotificationJob;
use app\models\ActiveRecord;
use app\models\Star;
use app\models\User;
use Yii;
use yii\base\Model;
use yii\base\BaseObject;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\queue\Queue;

abstract class BaseNotification extends BaseObject
{
    /**
     * @param ActiveRecord $model
     * @param User|null $updater
     * @return \Generator
     */
    protected function getFollowers($model, $updater = null)
    {
        $followers = Star::getFollowers($model)->each();
        foreach($followers as $follower) {
            /** @var $follower User */
            if ($updater !== null && $follower->equals($updater)) {
                continue;
            }
            yield $follower;
        }
    }

    /**
     * @param User $recipient
     * @param array $viewParams
     * @return \yii\mail\MessageInterface
     */
    protected function buildEmail($recipient, $viewParams)
    {
        $viewParams['user'] = $recipient;

        // view name for WikiUpdateNotification, will be "wiki-update"
        $viewName = Inflector::camel2id(str_replace('Notification', '', StringHelper::basename(get_class($this))));
        $message = Yii::$app->mailer->compose([
            'html' => "@app/notifications/views/$viewName.html.php",
            'text' => "@app/notifications/views/$viewName.text.php"
        ], $viewParams);
        $message->setSubject($this->getSubject());

        $message->setFrom(Yii::$app->params['notificationEmail']);
        $message->setTo([$recipient->email => $recipient->display_name]);

        return $message;
    }

    public static function create($params)
    {
        /** @var $queue Queue */
        $queue = Yii::$app->queue;
        foreach ($params as $param) {
            // clear validators which may contain closures, so objects can be serialized
            if ($param instanceof Model) {
                $validators = $param->getValidators();
                $validators->exchangeArray([]);
            }
        }
        $queue->push(new NotificationJob(['notification' => new static($params)]));
    }

    abstract public function notify();

    abstract protected function getSubject();
}
