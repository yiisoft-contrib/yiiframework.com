<?php

use yii\helpers\Html;

/** @var $dataProvider \yii\data\ActiveDataProvider */


$this->title = 'Wiki';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="guide-header-wrap">
    <div class="container guide-header common-heading">
        <div class="row">
            <div class="col-md-12">
                <h1 class="guide-headline"><?= Html::encode($this->title) ?></h1>
            </div>
        </div>
    </div>
</div>

<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar') ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <?= Html::beginForm(['wiki/revision', 'id' => $model->id], 'get') ?>

            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => \yii\grid\CheckboxColumn::class,
                        'name' => 'r',
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return ['value' => $model->revision];
                        },
                    ],
                    'revision',
                    [
                        'attribute' => 'memo',
                        'content' => function($model) {
                            return Html::a(empty($model->memo) ? Yii::$app->formatter->asText(null) : Html::encode($model->memo), ['wiki/view', 'id' => $model->wiki_id, 'revision' => $model->revision]);
                        },
                    ],
                    'updater_id',
                    'updated_at:datetime'
                ],
            ]) ?>

            <?= Html::submitButton('Compare Versions') ?>

            <?= Html::endForm() ?>

        </div>
    </div>
</div>