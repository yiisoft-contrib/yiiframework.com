<?php

namespace app\jobs;

use app\models\Extension;
use app\models\Wiki;
use Yii;
use Abraham\TwitterOAuth\TwitterOAuth;
use app\models\ContentShare;
use app\models\News;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\queue\closure\Job;
use yii\queue\Queue;

class ContentShareJob extends Job
{
    /**
     * @var int
     */
    public $objectTypeId;
    /**
     * @var int
     */
    public $objectId;
    /**
     * @var int
     */
    public $serviceId;

    /**
     * @param Queue $queue
     */
    public function execute($queue)
    {
        if (ContentShare::exists($this->objectTypeId, $this->objectId, $this->serviceId)) {
            return;
        }

        $contentData = $this->getContentData();
        if ($contentData === false) {
            return;
        }

        $this->send($contentData['message'], $contentData['url']);
    }

    /**
     * @return array|bool
     */
    protected function getContentData()
    {
        if ($this->objectTypeId == ContentShare::OBJECT_TYPE_NEWS) {
            $news = News::find()
                ->andWhere([
                    'id' => $this->objectId,
                    'status' => News::STATUS_PUBLISHED
                ])
                ->limit(1)
                ->one();

            if (!$news) {
                return false;
            }

            return [
                'message' => "News: {$news->title}",
                'url' => Url::to($news->getUrl(), true)
            ];
        }

        if ($this->objectTypeId == ContentShare::OBJECT_TYPE_WIKI) {
            $wiki = Wiki::find()
                ->andWhere([
                    'id' => $this->objectId,
                    'status' => Wiki::STATUS_PUBLISHED
                ])
                ->limit(1)
                ->one();

            if (!$wiki) {
                return false;
            }

            return [
                'message' => "Wiki: {$wiki->title}",
                'url' => Url::to($wiki->getUrl(), true)
            ];
        }

        if ($this->objectTypeId == ContentShare::OBJECT_TYPE_EXTENSION) {
            $extension = Extension::find()
                ->andWhere([
                    'id' => $this->objectId,
                    'status' => Extension::STATUS_PUBLISHED
                ])
                ->limit(1)
                ->one();

            if (!$extension) {
                return false;
            }

            return [
                'message' => "Ext: {$extension->name}",
                'url' => Url::to($extension->getUrl(), true)
            ];
        }

        return false;
    }

    /**
     * @param string $message
     * @param string $url
     */
    protected function send($message, $url)
    {
        switch ($this->serviceId) {
            case ContentShare::SERVICE_TWITTER:
                $this->sendToTwitter($message, $url);
                break;
        }
    }

    /**
     * @param string $message
     * @param string $url
     */
    protected function sendToTwitter($message, $url)
    {
        $params = Yii::$app->params;

        $twitter = new TwitterOAuth(
            $params['twitter.consumerKey'],
            $params['twitter.consumerSecret'],
            $params['twitter.accessToken'],
            $params['twitter.accessTokenSecret']
        );

        // The maximum message length is 140 characters. For URL you need 23 characters.
        $message = StringHelper::truncate($message, 108) . " {$url} #yii";
        $twitter->post('statuses/update', ['status' => $message]);

        $status = (int) $twitter->getLastHttpCode();
        if ($status === 200) {
            if (!ContentShare::push($this->objectTypeId, $this->objectId, $this->serviceId)) {
                Yii::error("Failed marking item: objectTypeId = {$this->objectTypeId}, objectId = {$this->objectId}, serviceId = {$this->serviceId} as executed.");
            }
        } else {
            Yii::error("Tweeting failed with status {$status}:\n" . $twitter->getLastBody());
        }
    }
}
