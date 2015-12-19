<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="row">
        <div class="site-books">
            <p>There are handy books about both Yii 2.0 and Yii 1.1 which could help you master the framework.</p>

            <h2>Yii 2.0</h2>
        </div>
    </div>
    <div class="row">
    <?php foreach($books2 as $book): ?>
        <?= $this->render('partials/books/_book', ['book' => $book]); ?>
    <?php endforeach ?>
    </div>
    <div class="row">
        <h2>Yii 1.1</h2>
    </div>
    <div class="row">
        <?php foreach($books1 as $book): ?>
            <?= $this->render('partials/books/_book', ['book' => $book]); ?>
        <?php endforeach ?>
    </div>
</div>
