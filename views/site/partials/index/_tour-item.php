<?php
/** @var array $tour_slide */
?>
<li class="glide__slide">
    <div class="row">
        <div class="col-md-7">
            <img src="<?= Yii::getAlias($tour_slide['image']) ?>"
                alt=""
                class="img-responsive"/>
        </div>
        <div class="col-md-5">
            <div class="tour-subheader">
                <?= $tour_slide['sub_title'] ?>
            </div>
            <div class="tour-header">
                <?= $tour_slide['title'] ?>
            </div>
            <div class="tour-content">
                <p>
                    <?= $tour_slide['content'] ?>
                </p>
            </div>
        </div>
    </div>
</li>
