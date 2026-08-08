<?php

use app\models\Wiki;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $dataProvider ActiveDataProvider */
/** @var $model Wiki */


$this->title = $model->title . ' | History';

?>
<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-xs-12" role="main">
            <div class="content wiki-row wiki-history-page">
                <h2 class="title"><?= Html::a(Html::encode($model->title), ['wiki/view', 'id' => $model->id, 'name' => $model->slug]) ?> - History</h2>

                <?= Html::beginForm(
                    ['wiki/revision', 'id' => $model->id],
                    'get',
                    ['id' => 'wiki-history-compare-form']
                ) ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'class' => CheckboxColumn::class,
                            'name' => 'r',
                            'header' => '',
                            'checkboxOptions' => static function ($model) {
                                return ['value' => $model->revision];
                            },
                        ],
                        'revision:integer:#',
                        [
                            'attribute' => 'memo',
                            'content' => static function ($model) {
                                return Html::a(empty($model->memo) ? Yii::$app->formatter->asText(null) : Html::encode($model->memo), ['wiki/view', 'id' => $model->wiki_id, 'revision' => $model->revision]);
                            },
                        ],
                        'updater.rankLink:raw:Updater',
                        'updated_at:datetime',
                        [
                            'label' => 'Actions',
                            'content' => static function ($model) {
                                return implode("<br>\n", [
                                    Html::a('View diff', ['wiki/revision', 'id' => $model->wiki_id, 'r1' => $model->revision]),
                                    Html::a('Revert to', ['wiki/update', 'id' => $model->wiki_id, 'revision' => $model->revision]),
                                ]);
                            },
                            'contentOptions' => [
                                'class' => 'action-column',
                            ],
                        ],
                    ],
                ]) ?>

                <p class="wiki-history-page__hint">Select exactly two versions for comparison.</p>

                <?= Html::submitButton('Compare Versions', [
                    'class' => 'btn btn-primary',
                    'disabled' => true,
                ]) ?>

                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
const historyCompareForm = document.getElementById('wiki-history-compare-form');

if (historyCompareForm) {
    const revisionCheckboxes = Array.from(historyCompareForm.querySelectorAll('input[name="r[]"]'));
    const compareButton = historyCompareForm.querySelector('button[type="submit"]');

    const updateRevisionSelection = () => {
        const selectedCount = revisionCheckboxes.filter((checkbox) => checkbox.checked).length;

        revisionCheckboxes.forEach((checkbox) => {
            checkbox.disabled = selectedCount >= 2 && !checkbox.checked;
        });
        compareButton.disabled = selectedCount !== 2;
    };

    revisionCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', updateRevisionSelection);
    });
    updateRevisionSelection();
}
JS
);
?>
