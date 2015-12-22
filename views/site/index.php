<?php
/* @var $this yii\web\View */
?>
<?= $this->render('partials/index/_jumbo', ['tour_slides' => $tour_slides]); ?>

<?= $this->render('partials/index/_featurepoints'); ?>

<?= $this->render('partials/index/_news'); ?>

<?= $this->render('partials/index/_booksvideos'); ?>

<div class="container content-separator wow fadeInUp">
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
