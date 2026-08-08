<?php

use app\components\UserPermissions;
use app\models\News;
use app\widgets\NewsTaglist;
use yii\bootstrap\Alert;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'News', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if (UserPermissions::canManageNews()) {
    $this->beginBlock('adminNav');
    echo Nav::widget([
        'id' => 'admin-nav',
        'items' => [
            ['label' => 'News Page', 'url' => ['news/index'] ],
            ['label' => 'News Admin', 'url' => ['news/admin'] ],
            ['label' => 'Update this news', 'url' => ['news/update', 'id' => $model->id, 'name' => $model->slug] ],
        ],
    ]);
    $this->endBlock();
}

?>
<div class="container style_external_links news-article">
    <div class="content news-content">

        <div class="row">
            <article class="col-md-9 news-article__main">

                <?php if (UserPermissions::canManageNews() && $model->status != News::STATUS_PUBLISHED) {

                    echo Alert::widget([
                        'body' =>
                            '<strong>News Status: </strong>' . Html::encode(News::getStatusList()[$model->status])
                            . ' &mdash; This post is not visibile to non-admins.',
                        'options' => ['class' => ($model->status == News::STATUS_DELETED ? 'alert-danger' : 'alert-info')],
                        'closeButton' => false,
                    ]);

                } ?>

                <header class="news-article__header">
                    <h1><?= Html::a(Html::encode($model->title), ['news/view', 'id' => $model->id, 'name' => $model->slug]) ?></h1>
                    <time class="date"><?= Yii::$app->formatter->asDate($model->news_date) ?></time>
                </header>
                <div class="text news-article__body">

                    <?= $model->contentHtml ?>

                </div>
            </article>
            <div class="col-md-3 news-sidebar">

                <?php if (UserPermissions::canManageNews()): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong>Admin Info</strong>
                        </div>
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                'slug',
                                'statusName',
                                'created_at:datetime',
                                'creator_id',
                                'updated_at:datetime',
                                'updater_id',
                            ]
                        ]) ?>
                    </div>

                <?php endif; ?>

                <h2>Related</h2>

                <ul>
                <?php foreach($model->relatedNews as $news) {
                    echo '<li>' . Html::a(
                        Html::encode($news->title),
                        ['news/view', 'id' => $news->id, 'name' => $news->slug]
                    ). '</li>';
                }
                ?>
                </ul>

                <?= NewsTaglist::widget(['news' => $model]) ?>

            </div>
        </div>

    </div>
</div>
