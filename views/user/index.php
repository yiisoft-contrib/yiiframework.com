<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Members';

echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
    'menu' => [
        ['label' => 'User Admin', 'url' => ['user-admin/index'], 'visible' => Yii::$app->user->can('news:pAdmin') ],
    ]
]);

$this->registerMetaTag(['name' => 'keywords', 'value' => 'yii framework, community, members']);

?>
<div class="container style_external_links">
    <div class="content">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => 'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{member} other{members}}.',
            'columns' => [
                [
                    'attribute' => 'rank',
                    'value' => function($model) {
                        return $model->rank == 999999 ? 'not ranked' : $model->rank;
                    },
                ],
                [
                    'attribute' => 'display_name',
                    'content' => function($model) {
                        return $model->rankLink;
                    },
                ],
                [
                    'attribute' => 'joined',
                    'value' => 'created_at',
                    'format' => 'date',
                    'label'=>'Member Since',
                ],
     			'rating',
                [
                    'attribute' => 'extensions',
                    'value' => 'extension_count',
                ],
                [
                    'attribute' => 'wiki',
                    'value' => 'wiki_count',
                ],
                [
                    'attribute' => 'comments',
                    'value' => 'comment_count',
                ],
                [
                    'attribute' => 'posts',
                    'value' => 'post_count',
                ],
            ],
        ]); ?>

    </div>
</div>
