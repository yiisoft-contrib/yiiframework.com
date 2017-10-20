<?php

namespace app\jobs;

use Yii;
use app\models\ContentShare;
use yii\queue\closure\Job;
use yii\queue\Queue;
use yii\queue\RetryableJob;

class ContentShareJob extends Job implements RetryableJob
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
