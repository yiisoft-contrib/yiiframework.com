<?php

use yii\helpers\Html;

/** @var $model app\models\Wiki the data model */
/** @var $extended bool */

?>
<div class="vote-box content">
    <div class="thumbs">
        <span class="up">
            <span class="votes">1</span>
            <a href="#"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>
        </span>
        <span class="down">
            <span class="votes">0</span>
            <a href="#"><i class="fa fa-thumbs-down" aria-hidden="true"></i></a>
        </span>
    </div>
    <div class="viewed"><span>Viewed:</span> <?= Yii::$app->formatter->asInteger($model->view_count) ?> times</div>
    <div class="version"><span>Version:</span> <?= Html::encode($model->yii_version) ?></div>
    <div class="group"><span>Category:</span> <?= Html::a(Html::encode($model->category->name), ['wiki/index', 'category' => $model->category_id]) ?></div>
    <div class="tags"><span>Tags:</span> <?= \app\widgets\WikiTaglist::widget(['wiki' => $model]) ?></div>
    <?php if ($extended): ?>
        <div class="people"><span>Written by:</span> <?= $model->creator->rankLink ?></div>
        <div class="people"><span>Last updated by:</span> <?= $model->updater->rankLink ?></div>
        <div class="dates"><span>Created on:</span> <?= Yii::$app->formatter->asDate($model->created_at) ?></div>
        <div class="dates"><span>Last updated:</span> <?= Yii::$app->formatter->asRelativeTime($model->updated_at) ?></div>
    <?php endif; ?>
</div>
