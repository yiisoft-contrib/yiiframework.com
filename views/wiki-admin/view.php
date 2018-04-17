<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Wiki */

$this->title = 'Wiki: ' . Html::encode($model->title);
$this->params['breadcrumbs'][] = ['label' => 'Wiki', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= $model->status == \app\models\Wiki::STATUS_PUBLISHED ? Html::a('View On Page', ['wiki/view', 'id' => $model->id, 'name' => $model->slug], ['class' => 'btn btn-default']) : '' ?>
    <?= Html::a('View Comments', ['comment-admin/index', 'CommentSearch[object_type]' => 'wiki', 'CommentSearch[object_id]' => $model->id], ['class' => 'btn btn-default']) ?>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?php /*= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ])*/ ?>
</p>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'title',
        [
            'attribute' => 'status',
            'value' => $model->status == \app\models\Wiki::STATUS_PUBLISHED ?
                '<span class="label label-success">Published</span>'
              : '<span class="label label-danger">Deleted</span>',
            'format' => 'raw',
        ],
        'yii_version',
        'category.name',
        [
            'attribute' => 'creator.username',
            'value' => function($model) {
                if ($model->creator === null) {
                    return Yii::$app->formatter->nullDisplay;
                }
                return Html::a(Html::encode($model->creator->username), ['user-admin/view', 'id' => $model->creator_id]);
            },
            'format' => 'raw',
            'label' => 'Creator',
        ],
        [
            'attribute' => 'updater.username',
            'value' => function($model) {
                if ($model->updater === null) {
                    return Yii::$app->formatter->nullDisplay;
                }
                return Html::a(Html::encode($model->updater->username), ['user-admin/view', 'id' => $model->updater_id]);
            },
            'format' => 'raw',
            'label' => 'Last updated by',
        ],
        'created_at:datetime',
        'updated_at:datetime',
    ],
]) ?>
<div class="content wiki-row">
     <h2 class="title"><?= Html::a(Html::encode($model->title), ['wiki/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>
     <div class="text">

         <?= $model->contentHtml ?>

     </div>
</div>
