<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Contribute';
$this->params['breadcrumbs'][] = $this->title;
?>

<main class="container content contribute-page style_external_links">
    <header class="contribute-page__intro">
        <h1>Contribute to Yii</h1>
        <p>Help improve the framework, its documentation, and the community around it.</p>
    </header>

    <div class="contribute-grid">
        <section class="contribute-path contribute-path--featured">
            <p class="contribute-path__eyebrow">Development</p>
            <h2>Contribute code</h2>
            <p>
                Pick a Yii package or tool, read its contribution guide, and look through the open issues. Discuss
                substantial changes with the maintainers before starting implementation.
            </p>
            <a class="contribute-path__action" href="https://github.com/orgs/yiisoft/repositories">Browse Yii repositories</a>
        </section>

        <section class="contribute-path contribute-path--featured">
            <p class="contribute-path__eyebrow">Issues</p>
            <h2>Report a problem</h2>
            <p>
                Search for an existing report, then provide affected versions, reproduction steps, and the smallest
                possible example. Use private reporting for vulnerabilities.
            </p>
            <div class="contribute-path__actions">
                <?= Html::a('Report an issue', ['site/report-issue'], ['class' => 'contribute-path__action']) ?>
                <?= Html::a('Security issue', ['site/security'], ['class' => 'contribute-path__link']) ?>
            </div>
        </section>

        <section class="contribute-path">
            <p class="contribute-path__eyebrow">Documentation</p>
            <h2>Improve the docs</h2>
            <p>Fix unclear wording, add examples, or document missing behavior. Guide pages include an edit link that opens the source on GitHub.</p>
            <?= Html::a('Open the Guide', ['guide/entry'], ['class' => 'contribute-path__action']) ?>
        </section>

        <section class="contribute-path">
            <p class="contribute-path__eyebrow">Ideas</p>
            <h2>Request a feature</h2>
            <p>Describe the problem first, explain who it affects, and include possible solutions or prior implementations.</p>
            <?= Html::a('Choose an issue tracker', ['site/report-issue'], ['class' => 'contribute-path__action']) ?>
        </section>

        <section class="contribute-path">
            <p class="contribute-path__eyebrow">Community</p>
            <h2>Spread the word</h2>
            <p>Share what you build, publish practical Yii knowledge, and help other developers find useful community resources.</p>
            <?= Html::a('Visit community places', ['site/community'], ['class' => 'contribute-path__action']) ?>
        </section>

        <section class="contribute-path">
            <p class="contribute-path__eyebrow">Funding</p>
            <h2>Support Yii</h2>
            <p>Donations help sustain project infrastructure and ongoing open-source development.</p>
            <?= Html::a('Donate to Yii', ['site/donate'], ['class' => 'contribute-path__action']) ?>
        </section>
    </div>
</main>
