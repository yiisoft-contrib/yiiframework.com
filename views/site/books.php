<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-books">
    <div class="container books">
        <div class="header container">
            <div class="row">
                <div class="col-md-6">
                    <h1>Books</h1>
                    <h2>Could help you master the framework</h2>
                </div>
                <div class="col-md-6">
                    <img class="background" src="<?= Yii::getAlias('@web/image/books/header.svg')?>" alt="">
                </div>
            </div>
        </div>
        <div class="row version">
            <h2><span>Yii 2.0</span></h2>
        </div>
        <div class="row">
            <?= $this->render('partials/_books', ['books' => $books2]) ?>
        </div>
        <div class="row version">
            <h2><span>Yii 1.1</span></h2>
        </div>
        <div class="row">
            <?= $this->render('partials/_books', ['books' => $books1]) ?>
        </div>
    </div>
</div>
