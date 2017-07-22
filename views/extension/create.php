<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\models\Wiki */

$this->title = 'Create Exension';
echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
//    'menu' => [
//        ['label' => 'News Page', 'url' => ['news/index'] ],
//        ['label' => 'News Admin', 'url' => ['news/admin'] ],
//        ['label' => 'Create News', 'url' => ['news/create'], 'active' => true ],
//    ]
]);
?>
<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar', [
                //'category' => $model->category_id,
                //'sort' => $dataProvider->sort,
            ]) ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
