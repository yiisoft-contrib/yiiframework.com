<?php

use yii\helpers\Html;

/** @var $model app\models\Wiki the data model */

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
    <div class="group"><span>Group:</span> <a href="#">Tips</a></div>
    <div class="tags"><span>Tags:</span> <?= \app\widgets\WikiTaglist::widget(['wiki' => $model]) ?></div>
</div>
