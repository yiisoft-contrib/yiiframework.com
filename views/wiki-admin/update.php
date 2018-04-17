<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Wiki */

$this->title = 'Update Wiki #' . $model->id . ' - ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Wiki', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "#$model->id - $model->title", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
