<?php

namespace app\jobs;

use Swift_RfcComplianceException;
use Swift_SwiftException;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

/**
 * EmailNotificationJob sends notification emails to users.
 */
class EmailNotificationJob extends BaseObject implements RetryableJobInterface
{
    /**
     * @var MessageInterface
     */
    public $message;

    public function init()
    {
        if ($this->message === null) {
            throw new InvalidConfigException('No email message set for EmailNotificationJob.');
        }
    }

    /**
     * @param Queue $queue which pushed and is handling the job
     */
    public function execute($queue)
    {
        echo "sending email to " . $this->formatEmail($this->message->getTo()) . ": " . $this->message->getSubject() . "\n";
        if (!Yii::$app->mailer->send($this->message)) {
            Yii::error('Failed to send email Message: ' . print_r($this->message, true));
            throw new Exception('Failed to send email Message.');
        }
    }

    private function formatEmail($email)
    {
        if (is_array($email)) {
            return implode(', ', array_keys($email));
        }
        return $email;
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr()
    {
        return 300; // 5 min
    }

    /**
     * @param int $attempt number
     * @param \Exception $error from last execute of the job
     * @return bool
     */
    public function canRetry($attempt, $error)
    {
        if ($error instanceof Swift_RfcComplianceException) {
            // the email is in invalid format
            return false;
        }
        if ($error instanceof Swift_SwiftException) {
            // failed to send email due to temporary error
            return true;
        }
        return false;
    }
}
