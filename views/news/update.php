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
        'items' => [
            ['label' => 'News Page', 'url' => ['news/index'] ],
            ['label' => 'News Admin', 'url' => ['news/admin'] ],
            ['label' => 'View this news', 'url' => ['news/view', 'id' => $model->id, 'name' => $model->slug] ],
        ],
    ]);
    $this->endBlock();
}

?>
<div class="container style_external_links">
    <div class="row">
        <div class="content">

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
