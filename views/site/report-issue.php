<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Report an Issue';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container content">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Thanks for helping to make Yii better!</p>

    <p>To ensure the issue gets reported to the right place, you need to find out whether it is an issue in the Yii framework
    itself, or in one of the official extensions.</p>

    <p>If you want to report a <b>security issue</b>, please consider contacting us privately so we can fix it before the details
    of the issue are publicly disclosed. <?= Html::a('Go to the contact page!', ['site/contact']) ?></p>

    <h3>Yii 2.0</h3>

    <p>General issues, reported on github in the <code>yiisoft/yii2</code> repository: <a href="https://github.com/yiisoft/yii2/issues/new">Report issue!</a></p>

    <h3>Yii 2.0 Extensions</h3>

    <p>If the error or feature request is for one of the official extensions, please select below:</p>

    <ul>
        <li><a href="https://github.com/yiisoft/yii2-apidoc/issues/new">API Documentation Generator</a> (<code>yiisoft/yii2-apidoc</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-authclient/issues/new">Auth client extension</a> (<code>yiisoft/yii2-authclient</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-bootstrap/issues/new">Bootstrap extension</a> (<code>yiisoft/yii2-bootstrap</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-codeception/issues/new">Codeception extension</a> (<code>yiisoft/yii2-codeception</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-composer/issues/new">Composer Installer</a> (<code>yiisoft/yii2-composer</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-debug/issues/new">Debug Toolbar</a> (<code>yiisoft/yii2-debug</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-elasticsearch/issues/new">Elasticsearch extension</a> (<code>yiisoft/yii2-elasticsearch</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-faker/issues/new">Faker extension</a> (<code>yiisoft/yii2-faker</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-gii/issues/new">Gii Code Generator</a> (<code>yiisoft/yii2-gii</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-jui/issues/new">jQuery UI extension</a> (<code>yiisoft/yii2-jui</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-mongodb/issues/new">Mongo DB extension</a> (<code>yiisoft/yii2-mongodb</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-redis/issues/new">redis extension</a> (<code>yiisoft/yii2-redis</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-smarty/issues/new">Smarty view renderer</a> (<code>yiisoft/yii2-smarty</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-sphinx/issues/new">Sphinx Search extension</a> (<code>yiisoft/yii2-sphinx</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-swiftmailer/issues/new">Swiftmailer extension</a> (<code>yiisoft/yii2-swiftmailer</code>)</li>
        <li><a href="https://github.com/yiisoft/yii2-twig/issues/new">Twig view renderer</a> (<code>yiisoft/yii2-twig</code>)</li>
    </ul>

    <p>For other extensions that are created by other users please check the extension page on where to report issues.</p>

    <h3>Yii 1.1</h3>

    <p>If you want to report a bug for Yii 1.1, please open and issue in the <code>yiisoft/yii</code> repository: <a href="https://github.com/yiisoft/yii/issues/new">Report issue!</a></p>

    <p>Please note that Yii 1.1 is in maintainance mode, we will not introduce big features anymore. Please consider upgrading to Yii 2 instead.</p>
</div>
