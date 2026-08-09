<?php

use app\components\Yii3PackageHelper;
use app\widgets\Comments;
use app\widgets\Star;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var string[] $versions all available API versions
 * @var string $version the currently chosen API version
 * @var string $content
 * @var string $section
 */

$this->registerJs("
    $(\"[data-toggle='offcanvas']\").click(function () {
      var isOpen = $('.row-offcanvas').toggleClass('active').hasClass('active');
      $(this).attr('aria-expanded', isOpen);
    });

    $('.has-children.active + div').addClass('active-parent');
");

if (!empty($title)) {
    $this->title = $title;
} elseif (!$content) {
    $this->title = "Yii $version API Documentation";
}

$this->beginBlock('contentSelectors');
echo $this->render('partials/_versions.php', [
    'version' => $version,
    'versions' => $versions,
    'section' => $section,
]);
$this->endBlock();

?>
<div class="container api-content<?= $content ? ' api-reference-page api-reference-page--3' : ' api-index-page' ?>">
    <div class="row visible-xs">
        <div class="col-md-12">
            <p class="pull-right topmost">
                <button type="button" class="btn btn-primary api-navigation-toggle" data-toggle="offcanvas"
                        aria-controls="api-navigation" aria-expanded="false">
                    API navigation
                </button>
            </p>
        </div>
    </div>

    <?php if ($content): ?>
        <?= strtr($content, [
            '<!-- YII_DOWNLOAD_OPTIONS -->' =>
            '<p>This page is also available in <a href="?_format=' . urlencode('json') . '">JSON format</a>:<br>'
                . '<code>curl ' . Url::to(['view', 'version' => $version, 'section' => $section], true) . ' -H \'Accept: application/json\'</code></p>',
            '<!-- YII_VERSION_SELECTOR -->' => isset($doc) ? '<div class="pull-right content">' . Star::widget(['model' => $doc]) . '</div>' : '',
        ]) ?>
    <?php else: ?>
        <header class="api-index-page__intro">
            <h1>Yii <?= Html::encode($version) ?> API documentation</h1>
            <p>Browse the API by package group. Each package contains its namespaces, classes, interfaces, traits, and functions.</p>
        </header>

        <div class="api-package-grid">
            <?php foreach (Yii3PackageHelper::PACKAGES_BY_GROUPS as $groupName => $packages): ?>
                <section class="package-group-panel">
                    <header class="package-group-panel__header">
                        <h2><?= Html::encode($groupName) ?></h2>
                    </header>
                    <?= Html::ul(
                        $packages,
                        [
                            'item' => static function (string $package) use ($version) {
                                $arrow = '<svg class="package-group-panel__arrow" viewBox="0 0 20 20" aria-hidden="true">'
                                    . '<path d="M7.5 4.5 13 10l-5.5 5.5-1.4-1.4 4.1-4.1-4.1-4.1 1.4-1.4Z"/>'
                                    . '</svg>';

                                return '<li>' . Html::a(Html::encode($package) . $arrow, [
                                        'view',
                                        'version' => $version,
                                        'section' => $package,
                                    ]) . '</li>';
                            },
                            'class' => 'package-group-panel__packages',
                        ]
                    ); ?>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($doc)): ?>
    <div class="comments-wrapper">
        <div class="container comments">
            <?= Comments::widget([
                'objectType' => $doc->getObjectType(),
                'objectId' => $doc->getObjectId(),
            ]) ?>
        </div>
    </div>
<?php endif ?>

<?php

$this->registerJs(
    <<<'JS'

$('.api-content a.toggle').on('click', function () {
    var $this = $(this);
    if ($this.hasClass('properties-hidden')) {
        $this.text($this.text().replace(/Show/,'Hide'));
        $this.parents('.toggle-target-container').find('.inherited').show();
        $this.removeClass('properties-hidden');
    } else {
        $this.text($this.text().replace(/Hide/,'Show'));
        $this.parents('.toggle-target-container').find('.inherited').hide();
        $this.addClass('properties-hidden');
    }

    return false;
});


JS
);
