<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 */
use app\components\DropdownList;
use app\components\SideNav;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$nav = [];
foreach ($guide->chapters as $chapterTitle => $sections) {
    $items = [];
    foreach ($sections as $sectionTitle => $sectionName) {
        $items[] = [
            'label' => $sectionTitle,
            'url' => ['guide/view', 'section' => $sectionName, 'language' => $guide->language, 'version' => $guide->version],
            'active' => $section->name === $sectionName,
        ];
    }
    $nav[] = [
        'label' => $chapterTitle,
        'items' => $items,
    ];
}

$this->title = $section->getPageTitle();
?>
<div class="guide-view">
    <div class="row">
        <div class="col-md-3">
            <?= SideNav::widget(['items' => $nav]) ?>
        </div>
        <div class="col-md-9 guide-content" role="main">
            <div class="row">
                <div class="col-sm-2">
                    <?= DropdownList::widget([
                        'selection' => "Version {$guide->version}",
                        'items' => array_map(function ($version) use ($section, $guide) {
                            return [
                                'label' => $version,
                                'url' => ['guide/view', 'section' => $section->name, 'version' => $version, 'language' => $guide->language],
                            ];
                        }, $guide->getVersionOptions()),
                    ]) ?>
                </div>
                <div class="col-sm-2">
                    <?= DropdownList::widget([
                        'selection' => $guide->getLanguageName(),
                        'items' => array_map(function ($language) use ($section, $guide) {
                            $options = $guide->getLanguageOptions();
                            return [
                                'label' => $options[$language],
                                'url' => ['guide/view', 'section' => $section->name, 'version' => $guide->version, 'language' => $language],
                            ];
                        }, array_keys($guide->getLanguageOptions())),
                    ]) ?>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                </div>
            </div>
            <?= $section->content ?>
        </div>
    </div>
</div>
