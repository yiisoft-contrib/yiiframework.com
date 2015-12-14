<?php
/* @var $this yii\web\View */
?>
<div class="site-index">
<!-- ==========================
JUMBOTRON - START
=========================== -->
<div class="jumbotron jumbotron-index">
    <div class="container">
		<h1>
            <span class="hidden-xs">
                <img src="<?= Yii::getAlias('@web/image/logo-light-80.png') ?>" alt="Yii Framework" />
            </span>
            <span class="visible-xs">
                <img src="<?= Yii::getAlias('@web/image/logo-light-42.png') ?>" alt="Yii Logo">
            </span>
        </h1>
        <h2>The solid foundation for your PHP application.</h2>

        <?= $this->render('_jumbofeatures'); ?>

    </div>
</div>

    <? //= $this->render('_features'); ?>

    <?= $this->render('_quickstart'); ?>

    <?= $this->render('_recentnews'); ?>

    <?= $this->render('_bookscommunity'); ?>

    <?= $this->render('_poweredby'); ?>

    <? //= $this->render('_testimonials'); ?>

</div> <!-- class site-index -->
