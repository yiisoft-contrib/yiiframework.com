<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 */
use app\components\DropdownList;
use app\components\SideNav;
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
$this->registerJs('
$(".sidenav-toggle").on("click", function() {
    var $button = $(this);
    $(".row-offcanvas").toggleClass("active");
    if ($button.text() === $button.data("show-text")) {
        $button.text($button.data("hide-text"));
    } else {
        $button.text($button.data("show-text"));
    }
});
');
?>
<div class="guide-view">
    <div class="row row-offcanvas">
        <div class="col-sm-3">
            <?= SideNav::widget(['items' => $nav, 'options' => ['class' => 'sidenav-offcanvas']]) ?>
        </div>
        <div class="col-sm-9 guide-content" role="main">
            <div class="navbar-header">
                <button type="button" class="sidenav-toggle btn btn-info" data-show-text="Navigate" data-hide-text="Close">Navigate</button>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <?= DropdownList::widget([
                        'selection' => "Version {$guide->version}",
                        'items' => array_map(function ($version) use ($guide) {
                            return [
                                'label' => $version,
                                'url' => ['guide/index', 'version' => $version, 'language' => $guide->language],
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

            <div class="prev-next clearfix">
                <?php
                if (($prev = $section->getPrevSection()) !== null) {
                    $left = '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> ';
                    echo '<div class="pull-left">' . Html::a($left . Html::encode($prev[1]), ['guide/view', 'section' => $prev[0], 'version' => $guide->version, 'language' => $guide->language]) . '</div>';
                }
                if (($next = $section->getNextSection()) !== null) {
                    $right = ' <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
                    echo '<div class="pull-right">' . Html::a(Html::encode($next[1]) . $right, ['guide/view', 'section' => $next[0], 'version' => $guide->version, 'language' => $guide->language]) . '</div>';
                }
                echo '<div class="text-center"><a href="#">Go to Top <span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a></div>';
                ?>
            </div>
        </div>
    </div>
</div>
