<?php


namespace app\widgets;


use app\models\Comment;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Recent Comments widget
 *
 * Could be used on any page like the following:
 *
 * ```php
 * <?= \app\widgets\RecentComments::widget([
         'objectType' => 'page',
         'objectId' => 'about',
     ]) ?>
 * ```
 */
class RecentComments extends Widget
{
    public $objectType;
    public $titleClass;
    public $menuClass;


    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->objectType === null) {
            throw new InvalidConfigException('objectType must be specified.');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $comments = Comment::find()
            ->recentComments($this->objectType, 5)
            ->active()
            ->with('user')->all();

        return $this->render('recent_comments', [
            'comments' => $comments,
            'titleClass' => $this->titleClass,
            'menuClass' => $this->menuClass,
        ]);
    }
}
