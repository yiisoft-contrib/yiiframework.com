<?php

namespace app\notifications;


use app\models\ActiveRecord;
use app\models\Comment;
use app\models\Linkable;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;

class CommentNewNotification extends BaseNotification
{
    /**
     * @var Linkable|ActiveRecord
     */
    public $model;
    /**
     * @var Comment
     */
    public $comment;


    public function init()
    {
        if (!$this->model instanceof Linkable) {
            throw new InvalidConfigException('Model instance passed to CommentNewNotification is invalid.');
        }
        if (!$this->comment instanceof Comment) {
            throw new InvalidConfigException('Comment instance passed to CommentNewNotification is invalid.');
        }
        parent::init();
    }

    /**
     * @return MessageInterface[]
     */
    public function notify()
    {
        foreach($this->getFollowers($this->model, $this->comment->user) as $user) {
            yield $this->buildEmail($user, [
                'model' => $this->model,
                'comment' => $this->comment,
            ]);
        }
    }

    protected function getSubject()
    {
        return "A new comment was added: " . $this->model->getLinkTitle();
    }
}
