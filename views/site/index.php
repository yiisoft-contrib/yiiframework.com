<?php
/* @var $this yii\web\View */
?>
<div class="site-index">
<!-- ==========================
JUMBOTRON - START
=========================== -->
<div class="jumbotron jumbotron-index">
    <div class="container">
        <a class="github-fork-ribbon right-top" href="https://github.com/yiisoft/yii2" title="Fork me on GitHub">Fork me on GitHub</a>
		<h1><img src="<?= Yii::getAlias('@web/image/yii_petals.svg') ?>" alt="Yii Framework" width="80" />yii<span class="hero-framework">framework</span></h1>
        <h2>The solid foundation for your PHP application.</h2>

        <?= $this->render('_jumbofeatures'); ?>

    </div>
</div>

    <?= $this->render('_features'); ?>

    <?= $this->render('_quickstart'); ?>

    <?= $this->render('_poweredby'); ?>

    <?= $this->render('_testimonials'); ?>

</div>
</div> <!-- class site-index -->
