<?php

use yii\bootstrap\Nav;
use yii\grid\ActionColumn;
use app\models\News;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'News Admin';

$this->beginBlock('adminNav');
echo Nav::widget([
    'id' => 'admin-nav',
    'items' => [
        ['label' => 'News Page', 'url' => ['news/index'] ],
        ['label' => 'News Admin', 'url' => ['news/admin'], 'active' => true ],
        ['label' => 'Create News', 'url' => ['news/create'] ],
    ],
]);
$this->endBlock();

?>
<div class="container style_external_links">
    <div class="content">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                'title',
                'news_date',
                [
                    'attribute' => 'status',
                    'value' => 'statusName',
                    'filter' => News::getStatusList(),
                ],
                'created_at',
                'updated_at',
                // 'creator_id',
                // 'updater_id',

                [
                    'class' => ActionColumn::class,
                    'contentOptions' => ['class' => 'action-column'],
                ],
            ],
        ]) ?>

    </div>
</div>
