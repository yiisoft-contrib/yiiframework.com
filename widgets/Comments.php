<?php


namespace app\widgets;


use app\models\Comment;
use app\models\Linkable;
use app\notifications\CommentNewNotification;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Comments widget
 *
 * Could be used on any page like the following:
 *
 * ```php
 * <?= \app\widgets\Comments::widget([
         'objectType' => 'page',
         'objectId' => 'about',
     ]) ?>
 * ```
 */
class Comments extends Widget
{
    public $objectType;
    public $objectId;

    public $prompt;

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

        if ($commentForm->load(Yii::$app->request->post())) {
            if( $commentForm->save()) {

                // notify followers
                $model = $commentForm->getModel();
                if ($model instanceof Linkable) {
                    $commentForm->refresh();
                    CommentNewNotification::create([
                        'model' => $model,
                        'comment' => $commentForm,
                    ]);
                }

                $id = $commentForm->id;
                // reset form
                $commentForm = $this->getNewCommentForm($this->objectType, $this->objectId);
                // take user to the newly created comment
                Yii::$app->getResponse()
                    ->refresh(sprintf("#c%d", $id))
                    ->send();
                Yii::$app->end();
            }
        }

        $comments = Comment::find()
            ->forObject($this->objectType, $this->objectId)
            ->active()
            ->with('user')->all();

        return $this->render('comments', [
            'comments' => $comments,
            'commentForm' => $commentForm,
            'prompt' => $this->prompt,
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
