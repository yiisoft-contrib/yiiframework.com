<?php

use yii\helpers\Html;

/** @var $model app\models\Extension the data model */
/** @var $extended bool */

?>
<div class="vote-box content">
    <?= \app\widgets\Voter::widget(['model' => $model]) ?>
    <?= \app\widgets\Star::widget(['model' => $model]) ?>

    <div class="star-wrapper">
        <?= Html::tag('i', '', [
            'class' => 'fa fa-download',
            'aria-hidden' => 'true',
        ]) ?>
        <span class="star-count"><?= Yii::$app->formatter->asInteger($model->download_count) ?></span> downloads
    </div>

    <div class="version"><span>Yii Version:</span> <?= empty($model->yii_version) ? 'Unknown' : Html::encode($model->yii_version) // TODO permission ?></div>
    <div class="people"><span>License:</span> <?= $model->getLicenseLink() ?></div>

    <div class="group"><span>Category:</span> <?= Html::a(Html::encode($model->category->name), ['extension/index', 'category' => $model->category_id]) ?></div>
    <div class="tags"><span>Tags:</span> <?= \app\widgets\ExtensionTaglist::widget(['extension' => $model]) ?></div>
    <?php if ($extended): ?>
        <div class="people"><span>Developed by:</span> <?= $model->owner->rankLink ?></div>
        <div class="dates"><span>Created on:</span> <?= Yii::$app->formatter->asDate($model->created_at) ?></div>
        <div class="dates"><span>Last updated:</span> <?= Yii::$app->formatter->asRelativeTime($model->updated_at) ?></div>
    <?php endif; ?>
</div>
