<?php

use yii\helpers\Html;
use yii\helpers\Markdown;

/** @var $model app\models\News */

?>
<div>
    <span class="date"><?= Yii::$app->formatter->asDate($model->news_date) ?></span>
    <h2><?= Html::a(Html::encode($model->title), ['news/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>
    <div class="text">
        <p><?= Markdown::process($model->getTeaser(), 'gfm') ?></p>
    </div>
</div>
