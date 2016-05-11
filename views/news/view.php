<?php

use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'News', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
    'menu' => Yii::$app->user->can('news:pAdmin') ? [
        ['label' => 'News page', 'url' => ['news/index'] ],
        ['label' => 'News admin', 'url' => ['news/admin'] ],
        ['label' => 'Update this news', 'url' => ['news/update', 'id' => $model->id, 'name' => $model->slug] ],
    ] : [],
]);
?>
<div class="container style_external_links">
    <div class="row">
        <div class="content news-view">

            <div class="row">
                <div class="col-md-9">

                    <?php if (Yii::$app->user->can('news:pAdmin')) {

                        echo \yii\bootstrap\Alert::widget([
                            'body' =>
                                '<strong>News Status: </strong>' . Html::encode(\app\models\News::getStatusList()[$model->status])
                                . ($model->status != \app\models\News::STATUS_PUBLISHED ? ' &mdash; This is a preview, not visibile to non-admins.' : ''),
                            'options' => ['class' => 'alert-info']
                        ]);

                    } ?>

                    <span class="date"><?= Yii::$app->formatter->asDate($model->news_date) ?></span>
                    <div class="text">


                        <?= Markdown::process($model->content, 'gfm') ?>

                    </div>
                </div>
                <div class="col-md-3">
                    <h2>Related News</h2>

                    <p>TODO</p>

                    <h2>News Archive</h2>

                    <p>TODO</p>

                    <h2>Tags</h2>

                    <p>TODO</p>

                </div>
            </div>

        </div>
    </div>
</div>
