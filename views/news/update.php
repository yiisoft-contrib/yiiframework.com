<?php

use app\components\UserPermissions;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = 'Update News: ' . $model->title;

if (UserPermissions::canManageNews()) {
    $this->beginBlock('adminNav');
    echo Nav::widget([
        'id' => 'admin-nav',
        'options' => ['class' => 'news-admin-actions'],
        'items' => [
            ['label' => 'News Page', 'url' => ['news/index'] ],
            ['label' => 'News Admin', 'url' => ['news/admin'] ],
            ['label' => 'View this news', 'url' => ['news/view', 'id' => $model->id, 'name' => $model->slug], 'linkOptions' => ['class' => 'news-admin-actions__primary'] ],
        ],
    ]);
    $this->endBlock();
}

?>
<div class="container style_external_links editor-page news-editor-page">
    <div class="content">
        <header class="news-editor-page__header">
            <p>Newsroom</p>
            <h1>Update news</h1>
            <span><?= \yii\helpers\Html::encode($model->title) ?></span>
        </header>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>
