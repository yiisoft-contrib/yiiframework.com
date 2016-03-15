<?php
use yii\helpers\Url;
?>

<div class="container content-separator latest-news">
    <div class="row">
        <div class="col-md-12">
            <div class="dashed-heading-front">
                <span>Latest News</span>
            </div>

            <div class="row news">
                <div class="col-md-6">
                    <span class="date">MAR 4, 2016</span>
                    <h2><a href="#">Yii fork of jquery-pjax version 2.0.6 released</a></h2>
                    <div class="text">
                        <p>Yii fork of jquery-pjax just had a new release fixing bugs, adding enhancements and new options...</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <span class="date">FEB 14, 2016</span>
                    <h2><a href="#">Yii 2.0.7 is released</a></h2>
                    <div class="text">
                        <p>We are very pleased to announce the release of Yii Framework version 2.0.7. Please refer to the instructions at http://www.yiiframework.com/download/ to install or upgrade to this version.</p>
                    </div>
                </div>
            </div>

            <div class="row news last">
                <div class="col-md-6">
                    <span class="date">NOV 22, 2015</span>
                    <h2><a href="#">Update on Yii 1.1 support and end of life</a></h2>
                    <div class="text">
                        <p>Yii 1.1 support was originally announced as "Dec 31, 2015 (may be extended further if needed)". In March we've conducted a survey and since December is close, it's time to officially announce our decision.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <span class="date">NOV 19, 2015</span>
                    <h2><a href="#">New member joining Yii team</a></h2>
                    <div class="text">
                        <p>We are very glad to announce that we have a new member joining the core team. His name is Dmitry Naumenko and his GitHub ID is SilverFire. Dmitry is from Kiev, Ukraine. Despite not being long time contributor he displayed very good sense of the framework, thorough approach to development and issue discussion and great technical skills.</p>
                    </div>
                </div>
            </div>

            <a href="<?= Url::to(['site/news']) ?>" class="btn btn-front btn-block">Read all news</a>
        </div>
    </div>
</div>
