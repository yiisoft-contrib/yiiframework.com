<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WikiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wiki';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'id',
            'content' => function($model) {
                return Html::a(Html::encode($model->id), ['wiki-admin/view', 'id' => $model->id]);
            },
        ],
        [
            'attribute' => 'title',
            'content' => function($model) {
                return Html::a(Html::encode($model->title), ['wiki-admin/view', 'id' => $model->id]);
            },
        ],
        [
            'attribute' => 'status',
            'content' => function($model) {
                return $model->status == \app\models\Wiki::STATUS_PUBLISHED ?
                    '<span class="label label-success">Published</span>'
                  : '<span class="label label-danger">Deleted</span>';
            },
            'filter' => \app\models\WikiSearch::getStatuses(),
        ],
        [
            'attribute' => 'category.name',
            'label' => 'Category',
            'filter' => \app\models\WikiSearch::getCategoryFilter(),
        ],
        'yii_version',
        [
            'attribute' => 'creator.username',
            'content' => function($model) {
                if ($model->creator === null) {
                    return Yii::$app->formatter->nullDisplay;
                }
                return Html::a(Html::encode($model->creator->username), ['user-admin/view', 'id' => $model->creator_id]);
            },
            'label' => 'Creator',
        ],
        [
            'attribute' => 'updater.username',
            'content' => function($model) {
                if ($model->updater === null) {
                    return Yii::$app->formatter->nullDisplay;
                }
                return Html::a(Html::encode($model->updater->username), ['user-admin/view', 'id' => $model->updater_id]);
            },
            'label' => 'Last Updated by',
        ],
        'created_at:datetime',
        'updated_at:datetime',
        [
            'class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['class' => 'action-column'],
        ],
    ],
]); ?>
