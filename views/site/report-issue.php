<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Report an Issue';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="content">
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
                <?php

                    $extensions = [
                        'yii2-apidoc' => 'API Documentation Generator',
                        'yii2-authclient' => 'Auth client extension',
                        'yii2-bootstrap' => 'Bootstrap extension',
                        'yii2-codeception' => 'Codeception extension',
                        'yii2-composer' => 'Composer Installer',
                        'yii2-debug' => 'Debug Toolbar',
                        'yii2-elasticsearch' => 'Elasticsearch extension',
                        'yii2-faker' => 'Faker extension',
                        'yii2-gii' => 'Gii Code Generator',
                        'yii2-jui' => 'jQuery UI extension',
                        'yii2-mongodb' => 'Mongo DB extension',
                        'yii2-redis' => 'redis extension',
                        'yii2-smarty' => 'Smarty view renderer',
                        'yii2-sphinx' => 'Sphinx Search extension',
                        'yii2-swiftmailer' => 'Swiftmailer extension',
                        'yii2-twig' => 'Twig view renderer',
                    ];

                    foreach($extensions as $ext => $extName) {
                        echo "<li><a href=\"https://github.com/yiisoft/{$ext}/issues/new\">{$extName}</a> (<code>yiisoft/{$ext}</code>)</li>\n";
                    }
                ?>
            </ul>

            <p>For other extensions that are created by other users please check the extension page on where to report issues.</p>

            <p>If you are unsure whether the issue you want to report belongs to an extension or the framework, just report it on the
                <a href="https://github.com/yiisoft/yii2/issues/new">framework issue tracker</a>. We will figure out where it belongs and move it later.</p>

            <h3>Yii 1.1</h3>

            <p>If you want to report a bug for Yii 1.1, please open an issue in the <code>yiisoft/yii</code> repository: <a href="https://github.com/yiisoft/yii/issues/new">Report issue!</a></p>

            <p>Please note that Yii 1.1 is in maintainance mode, we will not introduce big features anymore. Please consider upgrading to Yii 2 instead.</p>
        </div>
    </div>
</div>
