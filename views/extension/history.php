<?php

use yii\helpers\Html;

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $model \app\models\Wiki */


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
            <?= $this->render('_sidebar', [
                'category' => $model->category_id,
            ]) ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <div class="row">
                <div class="col-md-12 col-lg-9">
                    <div class="content wiki-row">
                        <h2 class="title"><?= Html::a(Html::encode($model->title), ['wiki/view', 'id' => $model->id, 'name' => $model->slug]) ?> - History</h2>

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
                                'revision:integer:#',
                                [
                                    'attribute' => 'memo',
                                    'content' => function($model) {
                                        return Html::a(empty($model->memo) ? Yii::$app->formatter->asText(null) : Html::encode($model->memo), ['wiki/view', 'id' => $model->wiki_id, 'revision' => $model->revision]);
                                    },
                                ],
                                'updater.rankLink:raw:Updater',
                                'updated_at:datetime',
                                [
                                    'label' => 'Actions',
                                    'content' => function($model) {
                                        return implode("<br>\n", [
                                            Html::a('view diff', ['wiki/revision', 'id' => $model->wiki_id, 'r1' => $model->revision]),
                                            Html::a('revert to', ['wiki/update', 'id' => $model->wiki_id, 'revision' => $model->revision]),
                                        ]);
                                    },
                                    'contentOptions' => [
                                        'class' => 'action-column',
                                    ]
                                ]
                            ],
                        ]) ?>

                        <p>Select exactly two versions for comparison.</p>

                        <?= Html::submitButton('Compare Versions') ?>

                        <?= Html::endForm() ?>

                    </div>
                </div>
                <div class="col-md-12 col-lg-3">
                    <?= $this->render('_metadata.php', ['model' => $model, 'extended' => true]) ?>

                    <?= Html::a('Update Article', ['wiki/update', 'id' => $model->id])?>


                    <h3>Revisions</h3>

                    <?= $this->render('_revisions.php', ['model' => $model]) ?>
                </div>
            </div>


        </div>
    </div>
</div>