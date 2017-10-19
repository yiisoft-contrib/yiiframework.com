<?php

namespace app\models;

use app\jobs\TweetJob;
use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\queue\Queue;

/**
 * This is the model class for table "tweet".
 *
 * @property int $id
 * @property string $object_type
 * @property int $object_id
 * @property int $status
 * @property int $created_at
 * @property int $posted_at
 * @property string $message
 */
class Tweet extends ActiveRecord
{
    const STATUS_NEW = 10;
    const STATUS_PUBLISHED = 20;
    const STATUS_FAILED = 30;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tweet}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(false),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_type', 'object_id'], 'required'],
            [['object_id'], 'integer'],
            [['object_type'], 'string', 'max' => 255],
        ];
    }

    public static function fromTweetable(Tweetable $object)
    {
        $tweet = Tweet::find()->where([
            'object_id' => $object->getTweetedObjectID(),
            'object_type' => $object->getTweetedObjectType(),
        ])->one();

        if (!$tweet) {
            $tweet = new self();
            $tweet->object_id = $object->getTweetedObjectID();
            $tweet->object_type = $object->getTweetedObjectType();
        }

        return $tweet;
    }

    public function enqueue()
    {
        if ($this->save()) {
            /** @var $queue Queue */
            $queue = Yii::$app->queue;
            $queue->push(new TweetJob(['tweet_id' => $this->id]));
        } else {
            throw new Exception('Ubable to enqueue a tweet: ' . json_encode($this->getErrors()));
        }
    }

    /**
     * @inheritdoc
     * @return TweetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TweetQuery(get_called_class());
    }
}
