<?php
/* @var $comments app\models\Comment[] */

use yii\helpers\Html;

?>
<?php if (!empty($comments)): ?>
<h3>Recent Comments</h3>
<ul>
    <?php foreach ($comments as $comment): ?>
        <li>
            <?= $comment->user->getRankLink() ?>
            <?php $model = $comment->model;
            if ($model instanceof \app\models\Linkable) {
                echo ' on ' . Html::a($model->getLinkTitle(), $model->getUrl('view', ['#' => "c{$comment->id}"]));
            } ?>
            <span class="date"><?=Yii::$app->formatter->format($comment->created_at, 'relativeTime')?></span>
        </li>
    <?php endforeach ?>
</ul>
<?php endif ?>
