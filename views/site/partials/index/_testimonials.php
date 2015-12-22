<?php
use yii\helpers\Url;

$this->registerJs("
    $('#Glide2').glide({
        type: 'carousel',
        autoheight: true,
        autoplay: 10000,
    });
");
?>
<div class="testimonials">
    <div class="dashed-heading-front-section">
        <span>Testimonials</span>
    </div>
    <div class="row testimonial">
        <div id="Glide2" class="glide">
            <div class="glide__arrows">
                <button class="glide__arrow prev" data-glide-dir="<">prev</button>
                <button class="glide__arrow next" data-glide-dir=">">next</button>
            </div>
            <div class="glide__wrapper">
                <ul class="glide__track">
                    <?php foreach($testimonials as $testimonial): ?>
                        <li class="glide__slide">
                            <div class="testimonial-image">
                                <img src="<?= $testimonial['image'] ?>"
                                    alt=""
                                    class="img-responsive"/>
                            </div>
                            <div class="testimonial-title">
                                <?= $testimonial['title'] ?>
                            </div>
                            <div class="testimonial-description">
                                <?= $testimonial['description'] ?>
                            </div>
                            <div class="testimonial-quote">
                                <p>
                                    <?= $testimonial['quote'] ?>
                                </p>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="row padded-row">
        <a href="<?= Url::to(['site/projects']) ?>" class="btn btn-front btn-block">See more projects using Yii</a>
    </div>
</div>
