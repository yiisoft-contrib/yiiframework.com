<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Official Logo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="content">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>In the following, we provide downloading of the official logo for the Yii framework in different formats. The logo
               is licensed under a <a href="http://creativecommons.org/licenses/by-nd/3.0/">Creative Commons Attribution-No
               Derivative Works 3.0 Unported License</a>.</p>

            <div>
                <h2>Logo for dark backgrounds</h2>
                <div class="logo-download-dark">
                    <img src="<?= Yii::getAlias('@web/image/logo80.png') ?>" title="Yii logo for dark backgrounds" alt="Yii logo for dark backgrounds">
                     transparent background, 725 x 157 px.
                </div>

                <p>Download: <a href="<?= Yii::getAlias('@web/files/logo/yii.eps') ?>">SVG version</a>, <a href="<?= Yii::getAlias('@web/files/logo/yii.png') ?>">PNG version</a></p>
            </div>
            <div>
                <h2>Logo for light backgrounds</h2>
                <div class="logo-download-light">
                    <img src="<?= Yii::getAlias('@web/image/logo-light-80.png') ?>" title="Yii logo for light backgrounds" alt="Yii logo for light backgrounds">
                     transparent background, 725 x 157 px.
                </div>
                <p>Download: <a href="<?= Yii::getAlias('@web/files/logo/yii-bw.eps') ?>">SVG version</a>, <a href="<?= Yii::getAlias('@web/files/logo/yii-bw.png') ?>">PNG version</a></p>
            </div>
        </div>
    </div>
</div>
