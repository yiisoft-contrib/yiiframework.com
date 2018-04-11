<?php

use app\models\Comment;
use app\models\User;
use app\widgets\Voter;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Comment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="comments-wrapper">
<div class="comments">
<div class="component-comments lang-en" id="comments">

    <div class="row">
        <div class="col-md-12">
            <div class="comment">
                <div class="comment-header">
                    <div class="row" id="c<?= $model->id ?>">
                        <div class="col-md-1">
                            <a href="#c<?= $model->id ?>" class="comment-id">#<?= $model->id ?></a>
                        </div>
                        <div class="col-md-2 pull-right">
                            <?= Voter::widget(['model' => $model]) ?>
                        </div>
                    </div>
                </div>
                <div class="comment-body">
                    <div class="text">
                        <?php
                            echo Yii::$app->formatter->asCommentMarkdown($model->text);
                        ?>
                    </div>
                </div>
                <div class="comment-footer">
                    <?= $model->user ? $model->user->rankLink : User::DELETED_USER_HTML ?> at
                    <span class="date"><?=Yii::$app->formatter->format($model->created_at, 'datetime')?></span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<p>
    <?= Html::a('View in Context', $model->getUrl(), ['class' => 'btn btn-default']) ?>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
</p>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'user_id',
        'user.rankLink:raw:User',
        'object_type',
        'object_id',
        [
            'attribute' => 'status',
            'value' => function($model) {
                switch($model->status) {
                    case Comment::STATUS_ACTIVE:
                        return '<span class="label label-success">active</span>';
                    case Comment::STATUS_DELETED:
                        return '<span class="label label-danger">deleted</span>';
                }
                return '<span class="label label-default">unknown</span>';
            },
            'format' => 'raw',
        ],
        'created_at:datetime',
        'updated_at:datetime',
    ],
]) ?>
