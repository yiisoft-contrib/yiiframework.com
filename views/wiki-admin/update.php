<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Wiki $model */

$this->title = 'Update Wiki #' . $model->id . ' - ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Wiki', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "#$model->id - $model->title", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
