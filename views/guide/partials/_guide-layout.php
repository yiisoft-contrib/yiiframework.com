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
            $url = ['guide/view', 'section' => $sectionName, 'language' => $guide->language, 'version' => $guide->version, 'type' => $guide->typeUrlName];
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
echo $this->render('_versions.php', ['guide' => $guide, 'section' => $section ?? null]);
$this->endBlock();

if (isset($section)) {
    echo $this->render('_scrollspy.php', ['guide' => $guide, 'section' => $section, 'notes' => true]);
}
?>
<div class="container guide-view lang-<?= $guide->language ?>" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row visible-xs">
        <div class="col-md-12">
            <p class="pull-right topmost">
                <button type="button" title="Toggle Side-Nav" class="btn btn-primary btn-xs" data-toggle="offcanvas">SideNav</button>
            </p>
        </div>
    </div>
    <div class="row row-offcanvas">
        <div class="col-sm-2 col-md-2 col-lg-2">
            <?= SearchForm::widget([
                'type' => 'guide',
                'version' => $guide->version, // TODO verify search works for extensions
                'language' => $guide->language,
                'placeholder' => 'Search the Guide…',
            ]) ?>
            <?= SideNav::widget(['id' => 'guide-navigation', 'items' => $nav, 'options' => ['class' => 'sidenav-offcanvas']]) ?>
        </div>
    <div class="col-sm-10 col-md-10 col-lg-10" role="main">
        <div class="row">
        <div class="col-md-12 col-lg-11">

            <?= $content ?>

            </div>
        </div>
        </div>
    </div>
    </div>
</div>
