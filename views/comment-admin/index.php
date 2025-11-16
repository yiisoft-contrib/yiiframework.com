<?php

use app\models\Comment;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Comments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'content' => static function(Comment $model) {
                    if ($model->status == Comment::STATUS_DELETED) {
                        return Html::a(Html::encode($model->id), ['view', 'id' => $model->id]);
                    }
                    return Html::a(Html::encode($model->id), $model->getUrl());
                },
            ],
            [
                'attribute' => 'user.username',
                'value' => 'user.rankLink',
                'format' => 'raw',
            ],
            [
                'attribute' => 'object_type',
                'filter' => array_combine(Comment::$availableObjectTypes, Comment::$availableObjectTypes),
            ],
            'object_id',
            'text:ntext',
            [
                'attribute' => 'status',
                'content' => static function($model) {
                    switch($model->status) {
                        case Comment::STATUS_ACTIVE:
                            return '<span class="label label-success">active</span>';
                        case Comment::STATUS_DELETED:
                            return '<span class="label label-danger">deleted</span>';
                    }
                    return '<span class="label label-default">unknown</span>';
                },
            ],
            'created_at:datetime',
            //'updated_at',
            //'total_votes',
            //'up_votes',
            //'rating',

            [
                'class' => yii\grid\ActionColumn::class,
            ],
        ],
    ]) ?>
</div>
