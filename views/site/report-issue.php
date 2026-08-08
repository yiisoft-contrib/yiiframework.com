<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Report an Issue';
$this->params['breadcrumbs'][] = $this->title;

$extensions = [
    'yii2-apidoc' => 'API Documentation Generator',
    'yii2-authclient' => 'Auth Client',
    'yii2-bootstrap' => 'Bootstrap',
    'yii2-composer' => 'Composer Installer',
    'yii2-debug' => 'Debug Toolbar',
    'yii2-elasticsearch' => 'Elasticsearch',
    'yii2-faker' => 'Faker',
    'yii2-gii' => 'Gii Code Generator',
    'yii2-httpclient' => 'HTTP Client',
    'yii2-imagine' => 'Imagine',
    'yii2-jui' => 'jQuery UI',
    'yii2-mongodb' => 'MongoDB',
    'yii2-queue' => 'Queue',
    'yii2-redis' => 'Redis',
    'yii2-shell' => 'Shell',
    'yii2-smarty' => 'Smarty',
    'yii2-sphinx' => 'Sphinx Search',
    'yii2-swiftmailer' => 'Swiftmailer',
    'yii2-symfonymailer' => 'Symfony Mailer',
    'yii2-twig' => 'Twig',
];

$githubIcon = '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 .248c-5.525 0-10 4.477-10 10a9.998 9.998 0 0 0 6.838 9.487c.5.094.683-.215.683-.48 0-.238-.008-.867-.013-1.7-2.781.602-3.368-1.343-3.368-1.343-.455-1.154-1.112-1.462-1.112-1.462-.906-.62.07-.607.07-.607 1.004.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.913.831.09-.647.347-1.087.633-1.337-2.22-.25-4.555-1.11-4.555-4.942 0-1.092.388-1.983 1.03-2.683-.113-.253-.45-1.27.087-2.647 0 0 .837-.268 2.75 1.025.8-.223 1.65-.332 2.5-.338.85.006 1.7.115 2.5.338 1.9-1.293 2.737-1.025 2.737-1.025.538 1.378.2 2.394.1 2.647.638.7 1.025 1.591 1.025 2.683 0 3.842-2.337 4.688-4.562 4.933.35.3.675.914.675 1.85 0 1.339-.013 2.414-.013 2.739 0 .262.175.575.688.475A9.966 9.966 0 0 0 20 10.247c0-5.522-4.477-10-10-10Z"/></svg>';
?>

<main class="container content report-issue-page style_external_links">
    <header class="report-issue-page__intro">
        <h1>Report an Issue</h1>
        <p>Choose the project affected by the bug or feature request.</p>
        <p class="report-issue-page__security">
            Found a vulnerability? <?= Html::a('Report it privately', ['site/security']) ?> before disclosing it publicly.
        </p>
    </header>

    <section class="report-issue-section">
        <header>
            <h2>Yii3</h2>
            <p>Yii3 is composed of independently maintained packages.</p>
        </header>
        <div class="report-issue-section__body">
            <a class="report-issue-action" href="https://github.com/orgs/yiisoft/repositories"><?= $githubIcon ?><span>Find the affected package</span></a>
            <h3>Application templates</h3>
            <div class="report-issue-links">
                <a href="https://github.com/yiisoft/app/issues/new/choose"><strong>Web</strong><span>yiisoft/app</span></a>
                <a href="https://github.com/yiisoft/app-api/issues/new/choose"><strong>API</strong><span>yiisoft/app-api</span></a>
                <a href="https://github.com/yiisoft/app-console/issues/new/choose"><strong>Console</strong><span>yiisoft/app-console</span></a>
            </div>
        </div>
    </section>

    <section class="report-issue-section">
        <header>
            <h2>Yii 2</h2>
            <p>Report framework issues to the core repository.</p>
        </header>
        <div class="report-issue-section__body">
            <a class="report-issue-action" href="https://github.com/yiisoft/yii2/issues/new/choose"><?= $githubIcon ?><span>Report a Yii 2 issue</span></a>
            <h3>Official extensions</h3>
            <div class="report-issue-links report-issue-links--extensions">
                <?php foreach ($extensions as $repository => $name): ?>
                    <a href="https://github.com/yiisoft/<?= Html::encode($repository) ?>/issues/new/choose">
                        <strong><?= Html::encode($name) ?></strong><span>yiisoft/<?= Html::encode($repository) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <p class="report-issue-section__hint">
                For community extensions, use the tracker linked from the extension page. If unsure, report the issue
                to <a href="https://github.com/yiisoft/yii2/issues/new/choose">Yii 2</a> and the maintainers will redirect it.
            </p>
        </div>
    </section>

    <section class="report-issue-section">
        <header>
            <h2>Yii 1.1</h2>
            <p>Only security and PHP compatibility fixes are accepted.</p>
        </header>
        <div class="report-issue-section__body">
            <a class="report-issue-action" href="https://github.com/yiisoft/yii/issues/new/choose"><?= $githubIcon ?><span>Report a Yii 1.1 issue</span></a>
            <p class="report-issue-section__hint">
                Yii 1.1 is in <?= Html::a('maintenance mode', ['news/view', 'id' => 90]) ?>. Consider upgrading to Yii 2 or Yii3.
            </p>
        </div>
    </section>

    <section class="report-issue-section">
        <header>
            <h2>Website</h2>
            <p>Report problems with yiiframework.com.</p>
        </header>
        <div class="report-issue-section__body">
            <a class="report-issue-action" href="https://github.com/yiisoft-contrib/yiiframework.com/issues/new/choose"><?= $githubIcon ?><span>Report a website issue</span></a>
        </div>
    </section>
</main>
