<?php

/** @var $this yii\web\View */
use yii\helpers\Html;

$url = ['/search/global'];
if ($language = Yii::$app->request->get('language')) {
    $url['language'] = $language;
    $this->registerJs('yiiSearchLanguage = ' . \yii\helpers\Json::htmlEncode($language), \yii\web\View::POS_HEAD);
}
if ($version = Yii::$app->request->get('version')) {
    $url['version'] = $version;
    $this->registerJs('yiiSearchVersion = ' . \yii\helpers\Json::htmlEncode($version), \yii\web\View::POS_HEAD);
}

?>
        <?= Html::beginForm($url, 'get', ['id' => 'search-form', 'class' => 'navbar-form']) ?>
        <div class="form-group nospace">
            <div class="input-group">
                <input type="text" class="form-control" id="search" name="q" placeholder="Search&hellip;" autocomplete="off" value="<?= property_exists($this->context, 'searchQuery') ? Html::encode($this->context->searchQuery) : '' ?>">
            </div>
        </div>
        <div id="search-resultbox"></div>
        <?= Html::endForm() ?>
