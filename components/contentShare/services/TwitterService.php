<?php

namespace app\components\contentShare\services;

use Abraham\TwitterOAuth\TwitterOAuth;
use app\components\contentShare\EntityInterface;
use Yii;

/**
 * Service for working with twitter.
 *
 * Message limits:
 * The maximum message length is 140 characters. For URL you need 23 characters.
 *
 * @inheritdoc
 */
class TwitterService extends BaseService
{
    /**
     * @inheritdoc
     */
    public function publish()
    {
        $params = Yii::$app->params;

        $twitter = new TwitterOAuth(
            $params['twitter.consumerKey'],
            $params['twitter.consumerSecret'],
            $params['twitter.accessToken'],
            $params['twitter.accessTokenSecret']
        );

        $twitter->post('statuses/update', ['status' => $this->contentShare->message]);

        $status = (int) $twitter->getLastHttpCode();
        if ($status === 200) {
            return true;
        }
        Yii::error("Tweeting failed with status {$status}:\n" . var_export($twitter->getLastBody(), true));

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(EntityInterface $entity)
    {
        return $entity->getContentShareTwitterMessage();
    }
}
