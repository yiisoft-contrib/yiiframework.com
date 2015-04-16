<?php


namespace app\components;


use app\models\Comment;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Comments widget
 *
 * Could be used on any page like the following:
 *
 * ```php
 * <?= \app\components\Comments::widget([
         'objectType' => 'page',
         'objectId' => 'about',
     ]) ?>
 * ```
 */
class Comments extends Widget
{
    public $objectType;
    public $objectId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->objectType === null || $this->objectId === null) {
            throw new InvalidConfigException('Both objectType and objectId should be specified.');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $commentForm = $this->getNewCommentForm($this->objectType, $this->objectId);

        if ($commentForm->load(Yii::$app->request->post()) && $commentForm->save()) {
            // reset form
            $commentForm = $this->getNewCommentForm($this->objectType, $this->objectId);
        }

        $comments = Comment::find()->where([
            'status' => Comment::STATUS_ACTIVE,
            'object_type' => $this->objectType,
            'object_id' => $this->objectId,
        ])->with('user')->all();

        return $this->render('comments', [
            'comments' => $comments,
            'commentForm' => $commentForm,
        ]);
    }

    /**
     * @param string $objectType
     * @param string $objectId
     *
     * @return Comment
     */
    protected function getNewCommentForm($objectType, $objectId)
    {
        return new Comment([
            'object_type' => $objectType,
            'object_id' => $objectId,
        ]);
    }
}