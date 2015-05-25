<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available API versions
 * @var $version string the currently chosen API version
 * @var $section string the currently active API file
 * @var $content string the API page content
 */

$this->title = "API Documentation for Yii $version";
if (!empty($title)) {
    $this->title = "$title - $this->title";
}
?>

<div class="container api-content">
	<?= strtr($content, ['<!-- YII_VERSION_SELECTOR -->' => $this->render('_versions.php', compact('version', 'versions', 'section'))]) ?>
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
