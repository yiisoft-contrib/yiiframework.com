<?php

namespace app\jobs;

use Yii;
use app\models\ContentShare;
use yii\base\BaseObject;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

class ContentShareJob extends BaseObject implements RetryableJobInterface
{
    /**
     * @var int
     */
    public $contentShareId;

    /**
     * @param Queue $queue
     *
     * @throws JobException
     */
    public function execute($queue)
    {
        $contentShare = ContentShare::find()
            ->forPublishing()
            ->andWhere(['id' => $this->contentShareId])
            ->limit(1)
            ->one();

        if (!$contentShare) {
            return;
        }

        if (!$contentShare->publish()) {
            throw new JobException("Failed publishing the message: contentShareId = {$this->contentShareId}.");
        }
    }

    /**
     * @return int
     */
    public function getTtr()
    {
        return 300;
    }

    /**
     * @param int $attempt
     * @param \Exception $error
     *
     * @return bool
     */
    public function canRetry($attempt, $error)
    {
        return ($attempt < 3) && ($error instanceof JobException);
    }
}
