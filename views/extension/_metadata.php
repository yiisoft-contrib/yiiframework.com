<?php

use app\widgets\ExtensionTaglist;
use app\widgets\Star;
use app\widgets\Voter;
use yii\helpers\Html;

/** @var $model app\models\Extension the data model */
/** @var $extended bool */

$yiiVersions = $model->getYiiVersions();
$yiiVersionLinks = array_map(static function ($version) {
    return Html::a(Html::encode($version), ['extension/index', 'version' => $version]);
}, $yiiVersions);

?>
<div class="vote-box content">
    <?= Voter::widget(['model' => $model]) ?>
    <?= Star::widget(['model' => $model]) ?>

    <div class="star-wrapper">
        <?= Html::tag('i', '', [
            'class' => 'fa fa-download',
            'aria-hidden' => 'true',
        ]) ?>
        <span class="star-count"><?= Yii::$app->formatter->asInteger($model->download_count) ?></span>
    </div>

    <div class="version">
        <span>Yii Version<?= count($yiiVersions) > 1 ? 's' : '' ?>:</span>
        <?= empty($yiiVersionLinks) ? 'Unknown' : implode(', ', $yiiVersionLinks) ?>
    </div>
    <div class="people"><span>License:</span> <?= $model->getLicenseLink() ?></div>

    <div class="group"><span>Category:</span> <?= Html::a(Html::encode($model->category->name), ['extension/index', 'category' => $model->category_id]) ?></div>
    <div class="tags"><?= ExtensionTaglist::widget(['extension' => $model]) ?></div>
    <?php if ($extended): ?>
        <div class="people"><span>Developed by:</span> <?= $model->getOwnerLink() ?></div>
        <div class="dates"><span>Created:</span> <?= Yii::$app->formatter->asDate($model->created_at) ?></div>
        <?php if ($model->updated_at): ?>
            <div class="dates"><span>Updated:</span> <?= Yii::$app->formatter->asRelativeTime($model->updated_at) ?></div>
        <?php endif; ?>
    <?php endif; ?>
</div>
