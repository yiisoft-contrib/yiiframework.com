<?php

use app\components\UserPermissions;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap4\Nav;

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
<div class="container style_external_links">
    <div class="content news-content">

        <div class="row">
            <div class="col-md-9">

                <?php if (UserPermissions::canManageNews() && $model->status != \app\models\News::STATUS_PUBLISHED) {

                    echo \yii\bootstrap4\Alert::widget([
                        'body' =>
                            '<strong>News Status: </strong>' . Html::encode(\app\models\News::getStatusList()[$model->status])
                            . ' &mdash; This post is not visibile to non-admins.',
                        'options' => ['class' => ($model->status == \app\models\News::STATUS_DELETED ? 'alert-danger' : 'alert-info')],
                        'closeButton' => false,
                    ]);

                } ?>

                <span class="date"><?= Yii::$app->formatter->asDate($model->news_date) ?></span>
                <h2><?= Html::a(Html::encode($model->title), ['news/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>
                <div class="text">

                    <?= $model->contentHtml ?>

                </div>
            </div>
            <div class="col-md-3">

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

                <h2>Related News</h2>

                <ul>
                <?php foreach($model->relatedNews as $news) {
                    echo '<li>' . Html::a(
                        Html::encode($news->title),
                        ['news/view', 'id' => $news->id, 'name' => $news->slug]
                    ). '</li>';
                }
                ?>
                </ul>

                <?= \app\widgets\NewsTaglist::widget(['news' => $model]) ?>

                <?= \app\widgets\NewsArchive::widget() ?>

            </div>
        </div>

    </div>
</div>
