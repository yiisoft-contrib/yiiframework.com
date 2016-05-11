<?php
/* @var $this yii\web\View */
/* @var $books array */
/* @var $tour_slides array */
/* @var $testimonials array */
/* @var $news app\models\News[] */
?>
<?= $this->render('partials/index/_jumbo'); ?>

<?= $this->render('partials/index/_featurepoints'); ?>

<?= $this->render('partials/index/_news', ['news' => $news]); ?>

<?= $this->render('partials/index/_booksvideos', ['books' => $books]); ?>

<div class="container content-separator">
    <div class="row">
        <div class="col-md-4">
            <?= $this->render('partials/index/_testimonials', ['testimonials' => $testimonials]); ?>
        </div>
        <div class="col-md-4">
            <?= $this->render('partials/index/_tutorials'); ?>
        </div>
        <div class="col-md-4">
            <?= $this->render('partials/index/_extensions'); ?>
        </div>
    </div>
</div>
