<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available API versions
 * @var $version string the currently chosen API version
 * @var $section string the currently active API file
 * @var $content string the API page content
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJs("
    $(\"[data-toggle='offcanvas']\").click(function () {
      $('.row-offcanvas').toggleClass('active')
    });

    $('.has-children.active + div').addClass('active-parent');
");

$this->title = "API Documentation for Yii $version";
if (!empty($title)) {
    $this->title = "$title - $this->title";
}
?>
<div class="guide-header-wrap">
    <div class="container guide-header">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="guide-headline h1">API Documentation for Yii <?= $version ?></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-offset-7 col-md-5">
                <?= $this->render('partials/_versions.php', compact('version', 'versions', 'section')) ?>
            </div>
        </div>
    </div>
</div>
<div class="container api-content">
    <div class="row visible-xs">
        <div class="col-md-12">
            <p class="pull-right topmost">
                <button type="button" title="Toggle Side-Nav" class="btn btn-primary btn-xs" data-toggle="offcanvas">SideNav</button>
            </p>
        </div>
    </div>
	<?= strtr($content, [
        '<!-- YII_DOWNLOAD_OPTIONS -->' => '<p>You may download the API documentation for offline use: </p><ul>'
            . '<li>' . Html::a("yii-docs-{$version}-en.tar.bz2", ['guide/download', 'version' => $version, 'language' => 'en', 'format' => 'tar.bz2']) . '</li>'
            . '<li>' . Html::a("yii-docs-{$version}-en.tar.gz", ['guide/download', 'version' => $version, 'language' => 'en', 'format' => 'tar.gz']) . '</li>'
            . '</ul>'
            . '<p>This page is also available in <a href="?_format='.urlencode('json').'">JSON format</a>:<br>'
            . '<code>curl ' . Url::to(['', 'version' => $version], true) . ' -H \'Accept: application/json\'</code></p>',
    ]) ?>
</div>

<div class="container">
    <?= \app\components\Comments::widget([
        'objectType' => 'api',
        'objectId' => $version . '-' . $section,
    ]) ?>
</div>

<?php

$this->registerJs(<<<'JS'

$(".api-content a.toggle").on('click', function () {
    var $this = $(this);
    if ($this.hasClass('properties-hidden')) {
        $this.text($this.text().replace(/Show/,'Hide'));
        $this.parents(".summary").find(".inherited").show();
        $this.removeClass('properties-hidden');
    } else {
        $this.text($this.text().replace(/Hide/,'Show'));
        $this.parents(".summary").find(".inherited").hide();
        $this.addClass('properties-hidden');
    }

    return false;
});


JS
);
