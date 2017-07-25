<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Official Logos';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container style_external_links">
    <div class="row">
        <div class="content">
            <p>
                In the following, we provide downloading of the official logo for the Yii framework in different formats.
            </p>
            <p>
                The logo is licensed under a <a href="http://creativecommons.org/licenses/by-nd/3.0/">Creative Commons Attribution-No
               Derivative Works 3.0 Unported License</a>.
           </p>

           <div>
               <h2>Logo for dark backgrounds</h2>
               <div class="logo-download-dark">
                   <img src="<?= Yii::getAlias('@web/image/yii_logo_dark.svg') ?>" height="80" title="Yii logo for dark backgrounds" alt="Yii logo for dark backgrounds">
                    transparent background, 725 x 149 px.
               </div>

               <p>Download: <a href="<?= Yii::getAlias('@web/image/yii_logo_dark.svg') ?>">SVG version</a>, <a href="<?= Yii::getAlias('@web/image/yii_logo_dark.png') ?>">PNG version</a></p>
           </div>
           <div>
               <h2>Logo for light backgrounds</h2>
               <div class="logo-download-light">
                   <img src="<?= Yii::getAlias('@web/image/yii_logo_light.svg') ?>" height="80" title="Yii logo for light backgrounds" alt="Yii logo for light backgrounds">
                    transparent background, 725 x 149 px.
               </div>
               <p>Download: <a href="<?= Yii::getAlias('@web/image/yii_logo_light.svg') ?>">SVG version</a>, <a href="<?= Yii::getAlias('@web/image/yii_logo_light.png') ?>">PNG version</a></p>
           </div>
       </div>
    </div>
</div>
