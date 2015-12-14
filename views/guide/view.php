<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 */
use app\components\SideNav;
use yii\helpers\Html;

$nav = [];
foreach ($guide->chapters as $chapterTitle => $sections) {
    $items = [];
    foreach ($sections as $sectionTitle => $sectionName) {
        $items[] = [
            'label' => $sectionTitle,
            'url' => ['guide/view', 'section' => $sectionName, 'language' => $guide->language, 'version' => $guide->version, 'type' => $guide->typeUrlName],
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
  $(\'[data-toggle="offcanvas"]\').click(function () {
    $(\'.row-offcanvas\').toggleClass(\'active\')
  });
');
?>
<div class="container-fluid guide-view lang-<?= $guide->language ?>" xmlns="http://www.w3.org/1999/html">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-4 col-md-9 col-md-offset-3 col-lg-10 col-lg-offset-2">
            <h1 class="guide-headline"><?= Html::encode($guide->title) ?></h1>
        </div>
          <p class="pull-right visible-xs topmost">
            <button type="button" title="Toggle Side-Nav" class="btn btn-primary btn-xs" data-toggle="offcanvas">Nav</button>
          </p>
    </div>

    <div class="row row-offcanvas">
        <div class="col-sm-4 col-md-3 col-lg-2">
            <?= $this->render('_versions.php', ['guide' => $guide, 'section' => $section]) ?>
            <?= SideNav::widget(['id' => 'guide-navigation', 'items' => $nav, 'options' => ['class' => 'sidenav-offcanvas']]) ?>
        </div>
        <div class="col-sm-8 col-md-9 col-lg-10" role="main" id="top">

            <div class="row">
            <div class="col-md-12 col-lg-10">
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
                        $left = '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> ';
                        echo '<div class="pull-left">' . Html::a($left . Html::encode($prev[1]), ['guide/view', 'section' => $prev[0], 'version' => $guide->version, 'language' => $guide->language, 'type' => $guide->typeUrlName]) . '</div>';
                    }
                    if (($next = $section->getNextSection()) !== null) {
                        $right = ' <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
                        echo '<div class="pull-right">' . Html::a(Html::encode($next[1]) . $right, ['guide/view', 'section' => $next[0], 'version' => $guide->version, 'language' => $guide->language, 'type' => $guide->typeUrlName]) . '</div>';
                    }
                    echo '<div class="text-center"><a href="#top">Go to Top <span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a></div>';
                    ?>
                </div>

                <?php if (($editUrl = $section->editUrl) !== false): ?>
                <div class="edit-icon"><i class="fa fa-github"></i></div>
                <p class="lang-en">
                    <em>Found a typo or you think this page needs improvement?<br />
                        <a href="<?= $editUrl; ?>">Edit it on github</a>!</em>
                </p>
                <?php endif; ?>
            </div>
            </div>
            <div class="col-lg-2 visible-lg">
                <nav id="scrollnav" data-spy="affix" data-offset-top="60">
                    <ul class="nav hidden-xs hidden-sm">
                        <?php
                            echo '<li>' . Html::a($section->getTitle(), '#' . (isset($section->headings['id']) ? $section->headings['id'] : '')) . '</li>';
                            $sections = isset($section->headings['sections']) ? $section->headings['sections'] : [];
                            foreach($sections as $heading) {
                                echo '<li>' . Html::a(Html::encode(strip_tags($heading['title'])), '#' . $heading['id']);
                                if (isset($heading['sub'])) {
                                    echo '<ul class="nav">';
                                    foreach ($heading['sub'] as $subheading) {
                                        echo '<li class="subheading">' . Html::a(Html::encode(strip_tags($subheading['title'])), '#' . $subheading['id']) . '</li>';
                                    }
                                    echo "</ul>";
                                }
                                echo '</li>';
                            }
                        ?>
                    </ul>
                </nav>
            </div>
            </div>

            <?= \app\components\Comments::widget([
                'objectType' => 'guide',
                'objectId' => $section->name. '-' . $guide->version,
            ]) ?>
        </div>
    </div>
</div>
