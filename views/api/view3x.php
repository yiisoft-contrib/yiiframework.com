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
      $('.row-offcanvas').toggleClass('active')
    });

    $('.has-children.active + div').addClass('active-parent');
");

if (!empty($title)) {
    $this->title = $title;
}

$this->beginBlock('contentSelectors');
echo $this->render('partials/_versions.php', [
    'version' => $version,
    'versions' => $versions,
]);
$this->endBlock();

?>
<div class="container api-content">
    <div class="row visible-xs">
        <div class="col-md-12">
            <p class="pull-right topmost">
                <button type="button" title="Toggle Side-Nav" class="btn btn-primary btn-xs" data-toggle="offcanvas">
                    SideNav
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
        <h1>Yii Framework <?= $version ?> API Documentation</h1>
        <br />
        <div class="row">
            <?php $groupRows = array_chunk(Yii3PackageHelper::PACKAGES_BY_GROUPS, 4, true); ?>
            <?php foreach ($groupRows as $group): ?>
                <div class="row">
                    <?php foreach ($group as $groupName => $packages): ?>
                        <div class="col-sm-6 col-md-3">
                            <div class="package-group-panel">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><?= Html::encode($groupName) ?></h3>
                                </div>
                                <div class="panel-body">
                                    <?= Html::ul(
                                        $packages,
                                        [
                                            'item' => static function (string $package) use ($version) {
                                                return '<li>' . Html::a(Html::encode($package), [
                                                    'view',
                                                    'version' => $version,
                                                    'section' => $package,
                                                ]) . '</li>';
                                            },
                                            'class' => 'list-unstyled',
                                        ]
                                    ); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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
