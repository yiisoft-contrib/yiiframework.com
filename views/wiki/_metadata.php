<?php

use app\widgets\Star;
use app\widgets\Voter;
use app\widgets\WikiTaglist;
use yii\helpers\Html;

/** @var $model app\models\Wiki the data model */
/** @var $extended bool */

?>
<div class="vote-box content">
    <?= Voter::widget(['model' => $model]) ?>
    <?= Star::widget(['model' => $model]) ?>

    <div class="viewed"><span>Viewed:</span> <?= Yii::$app->formatter->asInteger($model->view_count) ?> times</div>
    <div class="version"><span>Version:</span> <?= empty($model->yii_version) ? 'Unknown (' . Html::a('update', ['wiki/update', 'id' => $model->id]) . ')' : Html::encode($model->yii_version) ?></div>
    <div class="group"><span>Category:</span> <?= Html::a(Html::encode($model->category->name), ['wiki/index', 'category' => $model->category_id]) ?></div>
    <div class="tags"><span>Tags:</span> <?= WikiTaglist::widget(['wiki' => $model]) ?></div>
    <?php if ($extended): ?>
        <div class="people"><span>Written by:</span> <?= $model->creator->rankLink ?></div>
        <?php if ($model->updater): ?>
            <div class="people"><span>Last updated by:</span> <?= $model->updater->rankLink ?></div>
        <?php endif ?>
        <div class="dates"><span>Created on:</span> <?= Yii::$app->formatter->asDate($model->created_at) ?></div>
        <?php if ($model->updated_at): ?>
            <div class="dates"><span>Last updated:</span> <?= Yii::$app->formatter->asRelativeTime($model->updated_at) ?></div>
        <?php endif ?>
    <?php endif; ?>
</div>
