<?php


namespace app\jobs;


use app\models\Tweet;
use Yii;
use yii\base\BaseObject;
use yii\queue\Job;
use yii\queue\Queue;

class TweetJob extends BaseObject implements Job
{
    /**
     * @var int
     */
    public $tweetId;

    /**
     * @param Queue $queue which pushed and is handling the job
     */
    public function execute($queue)
    {
        $tweet = Tweet::find()->forPublishing()->where(['id' => $this->tweetId])->one();
        if (!$tweet) {
            return;
        }

        $params = Yii::$app->params;
        $twitter = new TwitterOAuth(
             $params['twitter.consumerKey'],
             $params['twitter.consumerSecret'],
             $params['twitter.accessToken'],
             $params['twitter.accessTokenSecret']
         );


         $twitter->post('statuses/update', ['status' => $tweet->message]);
         if ($twitter->getLastHttpCode() == 200) {
             $tweet->status = Tweet::STATUS_PUBLISHED;
             $tweet->posted_at = time();
         } else {
             $tweet->status = Tweet::STATUS_FAILED;
         }
         $tweet->save();
    }
}
