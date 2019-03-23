<?php

use yii\helpers\Html;
use yii\helpers\Markdown;

/** @var $model app\models\News */

?>
<div>
    <h2><?= Html::a(Html::encode($model->title), ['news/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>
    <span class="date"><?= Yii::$app->formatter->asDate($model->news_date) ?></span>
    <div class="text">
        <p><?= Markdown::process($model->getTeaser(), 'gfm') ?></p>
        <p><?= Html::a('&raquo; read more', ['news/view', 'id' => $model->id, 'name' => $model->slug]) ?></p>
    </div>
</div>
