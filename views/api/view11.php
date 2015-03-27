<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available API versions
 * @var $version string the currently chosen API version
 * @var $section string the currently active API file
 * @var $content string the API page content
 */

$this->title = "Yii API Documentation $version";
?>

<div class="content api-content">
    <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-9" role="main">
            <?= $this->render('_versions.php', compact('version', 'versions', 'section')) ?>
            <?= $content ?>
        </div>
    </div>
</div>
