<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 * @var $extensionName string
 * @var $extensionVendor string
 */

use app\models\Doc;
use app\widgets\SideNav;
use yii\helpers\Html;

$nav = [];
foreach ($guide->chapters as $chapterTitle => $sections) {
    $items = [];
    foreach ($sections as $sectionTitle => $sectionName) {
        if (preg_match('~^https?://~', $sectionName)) {
            $url = $sectionName;
            $active = false;
        } else {
            $url = ['guide/extension-view', 'section' => $sectionName, 'language' => $guide->language, 'version' => $guide->version, 'name' => $extensionName, 'vendorName' => $extensionVendor];
            $active = $section->name === $sectionName;
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

$this->title = $section->getPageTitle();
$this->registerJs('
  $(\'[data-toggle="offcanvas"]\').click(function () {
    $(\'.row-offcanvas\').toggleClass(\'active\')
  });
');

$this->beginBlock('contentSelectors');
echo $this->render('partials/_versions.php', ['guide' => $guide, 'section' => $section, 'extensionName' => $extensionName, 'extensionVendor' => $extensionVendor]);
$this->endBlock();

echo $this->render('partials/_scrollspy.php', ['guide' => $guide, 'section' => $section]);

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
            <?= \app\widgets\SearchForm::widget([
                'type' => 'guide',
                'version' => isset($version) ? $version : '2.0',
                'language' => $guide->language,
                'placeholder' => 'Search the Guide…',
            ]) ?>
            <?= SideNav::widget(['id' => 'guide-navigation', 'items' => $nav, 'options' => ['class' => 'sidenav-offcanvas']]) ?>
        </div>
        <div class="col-sm-10 col-md-10 col-lg-10" role="main">
            <div class="row">
            <div class="col-md-12 col-lg-11">
                <div class="guide-content content">
                <?php if (!empty($missingTranslation)): ?>
                    <div class="alert alert-warning">
                        <strong>This section is not translated yet.</strong> <br />
                        Please read it in English and consider
                        <a href="https://github.com/yiisoft/yii2/blob/master/docs/internals/translation-workflow.md">
                        helping us with translation</a>.
                    </div>
                <?php endif ?>

                <?= $section->content ?>

                <div class="prev-next clearfix">
                    <?php
                    if (($prev = $section->getPrevSection()) !== null) {
                        $left = '<span class="chevron-left" aria-hidden="true"></span> ';
                        echo '<div class="pull-left">' . Html::a($left . Html::encode($prev[1]), ['guide/extension-view', 'section' => $prev[0], 'version' => $guide->version, 'language' => $guide->language, 'name' => $extensionName, 'vendorName' => $extensionVendor]) . '</div>';
                    }
                    if (($next = $section->getNextSection()) !== null) {
                        $right = ' <span class="chevron-right" aria-hidden="true"></span>';
                        echo '<div class="pull-right">' . Html::a(Html::encode($next[1]) . $right, ['guide/extension-view', 'section' => $next[0], 'version' => $guide->version, 'language' => $guide->language, 'name' => $extensionName, 'vendorName' => $extensionVendor]) . '</div>';
                    }
                    echo '<div class="text-center"><a href="#top">Go to Top <span class="chevron-up" aria-hidden="true"></span></a></div>';
                    ?>
                </div>

            </div>
            </div>
            </div>
        </div>
    </div>
</div>

