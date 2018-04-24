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
                      [
                          'attribute' => 'no_release_for',
                          'value' => function($model) {
                              return $model['no_release_for'] . ($model['no_release_for'] == 1 ? ' day' : ' days');
                          }
                      ],
                      'status:image',
                      [
                          'attribute' => 'diff',
                          'content' => function($model) {
                              $parts = explode('/', $model['diff']);
                              return Html::a(Html::encode(end($parts)), $model['diff']);
                          }
                      ],
                  ],
              ]) ?>
        </div>
</div>