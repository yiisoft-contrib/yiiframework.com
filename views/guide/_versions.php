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
        <?php if ($guide->type == 'guide') {
            $items = [];
            if ($guide->getDownloadFile('pdf') !== false) {
                $items[] = [
                    'label' => 'PDF',
                    'url' => ['guide/download', 'version' => $guide->version, 'language' => $guide->language, 'format' => 'pdf'],
                ];
            }
            if ($guide->getDownloadFile('tar.gz') !== false) {
                $items[] = [
                    'label' => 'Offline HTML (tar.gz)',
                    'url' => ['guide/download', 'version' => $guide->version, 'language' => $guide->language, 'format' => 'tar.gz'],
                ];
            }
            if ($guide->getDownloadFile('tar.bz2') !== false) {
                $items[] = [
                    'label' => 'Offline HTML (tar.bz2)',
                    'url' => ['guide/download', 'version' => $guide->version, 'language' => $guide->language, 'format' => 'tar.bz2'],
                ];
            }
            if (!empty($items)) {
                echo DropdownList::widget([
                    'tag' => 'li',
                    'selection' => 'Download',
                    'items' => $items,
                ]);
            }
        } ?>
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
