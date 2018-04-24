<?php
use yii\helpers\Html;
use yii\grid\GridView;

/** @var \yii\data\ArrayDataProvider $dataProvider */
?>

<div class="container">
        <div class="content">

            <?= GridView::widget([
                  'tableOptions' => [
                      'class' => 'table',
                  ],
                  'dataProvider' => $dataProvider,
                  'columns' => [
                      'repository',
                      'latest',
                      'no_release_for',
                      'status:image',
                      'diff:url'
                  ],
              ]) ?>
        </div>
</div>