<?php
use yii\helpers\Html;
use app\components\Highlight as HL;
?>
<!-- start of quickstart -->
<section class="section-quickstart">
   <div class="container">
    <div class="row">
        <div class="col-md-3">
            <a href="/tour" class="thumbnail"><img src="<?= Yii::getAlias('@web/image/front/tour.png') ?>" title="Quick Start" alt="Quick Start"/></a>
        </div>
        <div class="col-md-9">
            <h2>Quick Start</h2>
            <p>
                Creating a project with Yii can be done in less than 5 minutes by creating a project using Composer and a project template:
            </p>
            <?php HL::begin(['language'=>'bash']); ?>
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
            <?php HL::end(); ?>
            <p>
                For more information about how to get started with Yii quickly
            </p>
            <div class="row">
                <div class="col-md-3">
                    <a class="btn btn-block btn-primary" href="/tour" role="button">Take the Yii Tour</a>
                </div>
                <div class="col-md-3">
                    <a class="btn btn-block btn-warning" href="/doc/guide" role="button">Read the Guide</a>
                </div>
                <div class="col-md-3">
                    <a class="btn btn-block btn-success" href="#" role="button">Join the Community</a>
                </div>
            </div>
        </div>
    </div>
   </div>
</section>
<!-- end of quickstart -->
