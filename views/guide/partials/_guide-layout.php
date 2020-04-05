<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 */

use app\widgets\SearchForm;
use app\widgets\SideNav;

$nav = [];
foreach ($guide->chapters as $chapterTitle => $sections) {
    $items = [];
    foreach ($sections as $sectionTitle => $sectionName) {
        if (preg_match('~^https?://~', $sectionName)) {
            $url = $sectionName;
            $active = false;
        } else {
            if (isset($extensionName, $extensionVendor)) {
                $url = ['guide/extension-view', 'section' => $sectionName, 'language' => $guide->language, 'version' => $guide->version, 'name' => $extensionName, 'vendorName' => $extensionVendor];
            } else {
                $url = ['guide/view', 'section' => $sectionName, 'language' => $guide->language, 'version' => $guide->version, 'type' => $guide->typeUrlName];
            }
            $active = isset($section) && $section->name === $sectionName;
        }
        $items[] = [
            'label' => $sectionTitle,
            'url' => $url,
            'active' => $active,
        ];
    }
    $nav[] = [
        'label' => $chapterTitle,
        'items' => $items,
    ];
}

$this->title = isset($section) ? $section->getPageTitle() : $guide->title;
$this->registerJs('
  $(\'[data-toggle="offcanvas"]\').click(function () {
    $(\'.row-offcanvas\').toggleClass(\'active\')
  });
');

$this->beginBlock('contentSelectors');
echo $this->render('_versions.php', [
    'guide' => $guide,
    'section' => $section ?? null,
    'extensionName' => $extensionName ?? null,
    'extensionVendor' => $extensionVendor ?? null
]);
$this->endBlock();

if (isset($section)) {
    echo $this->render('_scrollspy.php', [
        'guide' => $guide,
        'section' => $section,
        'notes' => true,
        'missingTranslation' => !empty($missingTranslation),
    ]);
}
?>
<div class="container guide-view" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row visible-xs">
        <div class="col-md-12">
            <p class="pull-right topmost">
                <button type="button" title="Toggle Side-Nav" class="btn btn-primary btn-xs" data-toggle="offcanvas">SideNav</button>
            </p>
        </div>
    </div>
    <div class="row row-offcanvas">
        <div class="col-sm-3 col-md-3 col-lg-3 lang-<?= $guide->language ?>">
            <?php if (!isset($extensionName, $extensionVendor)) {
                // TODO search currently does not work for extensions
                echo SearchForm::widget([
                    'type' => 'guide',
                    'version' => $guide->version,
                    'language' => $guide->language,
                    'placeholder' => 'Search the Guide…',
                ]);
            } ?>
            <?= SideNav::widget(['id' => 'guide-navigation', 'items' => $nav, 'options' => ['class' => 'sidenav-offcanvas']]) ?>
        </div>
        <div class="col-sm-9 col-md-9 col-lg-9" role="main">

            <?= $content ?>
        </div>
    </div>
</div>
