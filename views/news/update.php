<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = 'Update News: ' . $model->title;
echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
    'menu' => [
        ['label' => 'News page', 'url' => ['news/index'] ],
        ['label' => 'News admin', 'url' => ['news/admin'] ],
        ['label' => 'View this news', 'url' => ['news/view', 'id' => $model->id, 'name' => $model->slug] ],
    ]
]);
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
