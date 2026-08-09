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
    'options' => ['class' => 'news-admin-actions'],
    'items' => [
        ['label' => 'News Page', 'url' => ['news/index'] ],
        ['label' => 'News Admin', 'url' => ['news/admin'], 'active' => true ],
        ['label' => 'Create News', 'url' => ['news/create'], 'linkOptions' => ['class' => 'news-admin-actions__primary'] ],
    ],
]);
$this->endBlock();

?>
<div class="container style_external_links admin-page news-admin-page">
    <div class="content">

        <header class="news-admin-page__header">
            <div>
                <p>Newsroom</p>
                <h1>Manage news</h1>
                <span>Review publication status, dates, and recent changes.</span>
            </div>
            <?= \yii\helpers\Html::a('Create news', ['news/create'], ['class' => 'btn btn-primary']) ?>
        </header>

        <div class="news-admin-page__table">
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
</div>
