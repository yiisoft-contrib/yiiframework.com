<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

/* @var $results yii\data\ActiveDataProvider */
/* @var $queryString string */
/* @var $language string */
/* @var $version string */

$this->title = 'Search results for &quot;' . Html::encode($queryString) . '&quot;';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <?= $this->render('partials/_versions', [
        'searchQuery' => $queryString,
        'language' => $language,
        'version' => $version,
    ])?>
    <div class="content">
        <h1><?= $this->title ?></h1>

        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $results,
            'itemView' => 'partials/_result',
        ]) ?>

    </div>
</div>
