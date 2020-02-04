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
if ($type = Yii::$app->request->get('type')) {
    $url['type'] = $type;
} elseif (isset($this->context->searchScope)) {
    $url['type'] = $this->context->searchScope;
}

$searchPlaceholder = trim(implode(' ', [
        'Search',
        \app\models\search\SearchActiveRecord::getTypeName($url['type'] ?? '')
    ])) . '&hellip;';

?>

<?= Html::beginForm($url, 'get', ['id' => 'search-form', 'class' => 'navbar-form form-inline my-2 my-lg-0']) ?>
<input aria-label="Search" type="text" class="form-control form-control-sm" id="search" name="q" placeholder="<?= $searchPlaceholder ?>"
       autocomplete="off" value="<?= property_exists($this->context, 'searchQuery') ? Html::encode($this->context->searchQuery) : '' ?>">
<div id="search-resultbox"></div>
<?= Html::endForm() ?>
