<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Official Logo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container content">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>In the following, we provide downloading of the official logo for the Yii framework in different formats. The logo
       is licensed under a <a href="http://creativecommons.org/licenses/by-nd/3.0/">Creative Commons Attribution-No
       Derivative Works 3.0 Unported License</a>.</p>

    <div>
        <img src="<?= Yii::getAlias('@web/files/logo/yii.png') ?>" title="Yii logo in color" width="363" height="79" alt="">

        <p>Download: <a href="<?= Yii::getAlias('@web/files/logo/yii.eps') ?>">EPS version</a>, <a href="<?= Yii::getAlias('@web/files/logo/yii.png') ?>">PNG version</a></p>
    </div>


    <div>
        <img src="<?= Yii::getAlias('@web/files/logo/yii-bw.png') ?>" title="Yii logo in black and white" width="363" height="79" alt="">
        <p>Download: <a href="<?= Yii::getAlias('@web/files/logo/yii-bw.eps') ?>">EPS version</a>, <a href="<?= Yii::getAlias('@web/files/logo/yii-bw.png') ?>">PNG version</a></p>
    </div>
</div>
