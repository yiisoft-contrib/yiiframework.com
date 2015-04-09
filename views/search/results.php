<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

/* @var $results yii\data\ActiveDataProvider */
/* @var $queryString yii\data\ActiveDataProvider */

$this->title = 'Search results for &quot;' . Html::encode($queryString) . '&quot;';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content container">
    <h1><?= $this->title ?></h1>

<?= \yii\widgets\ListView::widget([
    'dataProvider' => $results,
    'itemView' => '_result',
]) ?>

</div>
