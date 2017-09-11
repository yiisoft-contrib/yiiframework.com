<?php

use app\components\UserPermissions;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = 'Create News';

if (UserPermissions::canManageNews()) {
    $this->beginBlock('adminNav');
    echo \yii\bootstrap\Nav::widget([
        'id' => 'admin-nav',
        'items' => [
            ['label' => 'News Page', 'url' => ['news/index'] ],
            ['label' => 'News Admin', 'url' => ['news/admin'] ],
            ['label' => 'Create News', 'url' => ['news/create'], 'active' => true ],
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
