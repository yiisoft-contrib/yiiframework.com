<?php

namespace app\jobs;

use app\notifications\BaseNotification;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\queue\Job;
use yii\queue\Queue;

/**
 * NotificationJob generates emails to send for notifying user about changes in items they follow.
 */
class NotificationJob extends BaseObject implements Job
{
    /**
     * @var BaseNotification
     */
    public $notification;

    public function init()
    {
        if ($this->notification === null) {
            throw new InvalidConfigException('No notification set for NotificationJob.');
        }
    }

    /**
     * @param Queue $queue which pushed and is handling the job
     */
    public function execute($queue)
    {
        $i = 0;
        foreach ($this->notification->notify() as $message) {
            $queue->push(new EmailNotificationJob(['message' => $message]));
            ++$i;
        }
        echo "enqueued $i notification emails for '" . get_class($this->notification) . "'.\n";
    }
}
