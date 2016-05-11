<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'News Admin';
echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
    'menu' => [
        ['label' => 'News Index', 'url' => ['news/index'] ],
        ['label' => 'News Admin', 'url' => ['news/admin'], 'active' => true ],
        ['label' => 'Create News', 'url' => ['news/create'] ],
    ]
]);

?>
<div class="container style_external_links">
    <div class="row">
        <div class="content">

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'id',
                    'title',
                    'news_date',
                    'status',
                    'created_at',
                    'updated_at',
                    // 'creator_id',
                    // 'updater_id',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>

        </div>
    </div>
</div>
