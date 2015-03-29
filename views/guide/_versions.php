<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 */
use app\components\DropdownList;
use yii\helpers\Html;

?>
<nav class="version-selector" role="navigation">
    <ul>
        <?= DropdownList::widget([
            'tag' => 'li',
            'selection' => $guide->getLanguageName(),
            'items' => array_map(function ($language) use ($section, $guide) {
                $options = $guide->getLanguageOptions();
                if (isset($section)) {
                    $url = ['guide/view', 'section' => $section->name, 'version' => $guide->version, 'language' => $language];
                } else {
                    $url = ['guide/index', 'version' => $guide->version, 'language' => $language];
                }
                return [
                    'label' => $options[$language],
                    'url' => $url,
                ];
            }, array_keys($guide->getLanguageOptions())),
        ]) ?>
        <?= DropdownList::widget([
            'tag' => 'li',
            'selection' => "Version {$guide->version}",
            'items' => array_map(function ($version) use ($guide) {
                return [
                    'label' => $version,
                    'url' => ['guide/index', 'version' => $version, 'language' => $guide->language],
                ];
            }, $guide->getVersionOptions()),
        ]) ?>
    </ul>
</nav>
