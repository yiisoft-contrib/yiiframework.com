<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 */
use app\components\DropdownList;
use app\models\Guide;
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
                    $url = ['guide/view', 'section' => $section->name, 'version' => $guide->version, 'language' => $language, 'type' => $guide->typeUrlName];
                } else {
                    $url = ['guide/index', 'version' => $guide->version, 'language' => $language, 'type' => $guide->typeUrlName];
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
            'items' => array_map(function ($version) use ($section, $guide) {
                $language = $guide->language;
                $otherGuide = Guide::load($version, $language, $guide->type);
                if ($otherGuide === null) {
                    $language = 'en';
                    $otherGuide = Guide::load($version, $language, $guide->type);
                }
                if (isset($section) && $guide->version[0] === $version[0] && $otherGuide->loadSection($section->name) !== null) {
                    $url = ['guide/view', 'section' => $section->name, 'version' => $version, 'language' => $language, 'type' => $guide->typeUrlName];
                } else {
                    $url = ['guide/index', 'version' => $version, 'language' => $language, 'type' => $guide->typeUrlName];
                }
                return [
                    'label' => $version,
                    'url' => $url,
                ];
            }, $guide->getVersionOptions()),
        ]) ?>
    </ul>
</nav>
