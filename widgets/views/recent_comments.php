<?php
/* @var $comments app\models\Comment[] */
/* @var $titleClass string */
/* @var $menuClass string */

use yii\helpers\Html;

?>
<?php if (!empty($comments)): ?>
    <?= Html::beginTag('h3', ['class' => $titleClass]) ?>
        Recent Comments
    <?= Html::endTag('h3') ?>
<?= Html::beginTag('ul', ['class' => $menuClass]) ?>
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
<?= Html::endTag('ul') ?>
<?php endif ?>
