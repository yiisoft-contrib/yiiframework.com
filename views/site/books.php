<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="site-books">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>There are handy books about both Yii 2.0 and Yii 1.1 which could help you master the framework.</p>

            <h2>Yii 2.0</h2>
        </div>
    </div>
    <div class="row">
    <?php foreach($books2 as $book): ?>
        <?= $this->render('_book', ['book' => $book]); ?>
    <?php endforeach ?>
    </div>
    <div class="row">
        <h2>Yii 1.1</h2>
    </div>
    <div class="row">
        <?php foreach($books1 as $book): ?>
            <?= $this->render('_book', ['book' => $book]); ?>
        <?php endforeach ?>
    </div>
</div>
