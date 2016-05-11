<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = 'Create News';
echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
    'menu' => [
        ['label' => 'News Index', 'url' => ['news/index'] ],
        ['label' => 'News Admin', 'url' => ['news/admin'] ],
        ['label' => 'Create News', 'url' => ['news/create'], 'active' => true ],
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
