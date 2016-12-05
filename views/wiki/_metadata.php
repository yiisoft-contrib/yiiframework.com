<?php

use yii\helpers\Html;

/** @var $model app\models\Wiki the data model */
/** @var $extended bool */

?>
<div class="vote-box content">
    <?= \app\widgets\Voter::widget(['model' => $model]) ?>
    <?= \app\widgets\Star::widget(['model' => $model]) ?>

    <div class="viewed"><span>Viewed:</span> <?= Yii::$app->formatter->asInteger($model->view_count) ?> times</div>
    <div class="version"><span>Version:</span> <?= empty($model->yii_version) ? 'Unknown (' . Html::a('update', ['wiki/update', 'id' => $model->id]) . ')' : Html::encode($model->yii_version) ?></div>
    <div class="group"><span>Category:</span> <?= Html::a(Html::encode($model->category->name), ['wiki/index', 'category' => $model->category_id]) ?></div>
    <div class="tags"><span>Tags:</span> <?= \app\widgets\WikiTaglist::widget(['wiki' => $model]) ?></div>
    <?php if ($extended): ?>
        <div class="people"><span>Written by:</span> <?= $model->creator->rankLink ?></div>
        <div class="people"><span>Last updated by:</span> <?= $model->updater->rankLink ?></div>
        <div class="dates"><span>Created on:</span> <?= Yii::$app->formatter->asDate($model->created_at) ?></div>
        <div class="dates"><span>Last updated:</span> <?= Yii::$app->formatter->asRelativeTime($model->updated_at) ?></div>
    <?php endif; ?>
</div>
