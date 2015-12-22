<?php
$this->registerJs("
    $('#Glide').glide({
        type: 'carousel',
        autoheight: true,
        autoplay: 10000,
    });
");
?>
<div class="sitejumbo">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>The solid foundation for your PHP application</h1>
                <p class="propaganda">
                    <strong>Yii</strong> is <em>fast</em>, <em>secure</em> and <em>efficient</em> and works right out of the box using reasonable defaults.
                </p>
                <p class="propaganda">
                    The framework is easy to adjust to meet your needs, because Yii has been designed to be <em>flexible</em>
                </p>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="dashed-heading-jumbo">
                    <span><a href="#" class="btn">Download Yii</a> </span>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div id="Glide" class="glide">
            <div class="glide__wrapper">
                <ul class="glide__track">
                    <?php foreach($tour_slides as $tour_slide): ?>
                        <?= $this->render('/site/partials/index/_tour-item', ['tour_slide' => $tour_slide]); ?>
                    <?php endforeach ?>
                </ul>
            </div>
            <div class="glide__bullets hidden-xs"></div>
        </div>
    </div>
</div>
