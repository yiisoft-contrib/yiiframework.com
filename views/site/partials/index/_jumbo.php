<?php
use yii\helpers\Url;

$this->registerJs("
    $('#Glide').glide({
        type: 'carousel',
        autoheight: false,
        autoplay: 10000,
    });
    var slideButton = $('.glide__bullet');
    slideButton.attr('title', 'pagination');
");
?>
<div class="sitejumbo">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1>Yes, it is!</h1>
                <p class="propaganda">
                    Yii is a fast, secure, and efficient PHP framework.<br>
                    Flexible yet pragmatic. <br>
                    Works right out of the box. <br>
                    Has reasonable defaults.
                </p>
            </div>
            <div class="col-md-4 col-link column-button-set">
                <a href="<?= Url::to('partners') ?>" class="btn">Need a developer?</a>
                <a href="<?= Url::to('donate') ?>" class="btn">Donate</a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="dashed-heading-jumbo">
                    <span>
                        <a href="<?= Url::to(['doc/guide/2.0/en/start-installation']) ?>" class="btn">Get Started</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div id="Glide" class="glide">
            <div class="glide__wrapper">
                <ul class="glide__track">
                    <?= $this->render('/site/partials/tour/1') ?>
                    <?= $this->render('/site/partials/tour/2') ?>
                    <?= $this->render('/site/partials/tour/3') ?>
                    <?= $this->render('/site/partials/tour/4') ?>
                    <?= $this->render('/site/partials/tour/5') ?>
                </ul>
            </div>
            <div class="glide__bullets hidden-xs"></div>
        </div>
    </div>
</div>
