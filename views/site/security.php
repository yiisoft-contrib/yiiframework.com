<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Report a Security Issue';
$this->params['breadcrumbs'][] = $this->title;

$githubIcon = '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 .248c-5.525 0-10 4.477-10 10a9.998 9.998 0 0 0 6.838 9.487c.5.094.683-.215.683-.48 0-.238-.008-.867-.013-1.7-2.781.602-3.368-1.343-3.368-1.343-.455-1.154-1.112-1.462-1.112-1.462-.906-.62.07-.607.07-.607 1.004.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.913.831.09-.647.347-1.087.633-1.337-2.22-.25-4.555-1.11-4.555-4.942 0-1.092.388-1.983 1.03-2.683-.113-.253-.45-1.27.087-2.647 0 0 .837-.268 2.75 1.025.8-.223 1.65-.332 2.5-.338.85.006 1.7.115 2.5.338 1.9-1.293 2.737-1.025 2.737-1.025.538 1.378.2 2.394.1 2.647.638.7 1.025 1.591 1.025 2.683 0 3.842-2.337 4.688-4.562 4.933.35.3.675.914.675 1.85 0 1.339-.013 2.414-.013 2.739 0 .262.175.575.688.475A9.966 9.966 0 0 0 20 10.247c0-5.522-4.477-10-10-10Z"/></svg>';
?>

<main class="container content report-issue-page security-report-page style_external_links">
    <header class="report-issue-page__intro">
        <h1>Report a Security Issue</h1>
        <p>Report vulnerabilities privately to the maintainers of the affected project.</p>
        <p class="report-issue-page__security">
            Do not open a public issue or disclose the vulnerability before it has been addressed.
        </p>
    </header>

    <section class="report-issue-section">
        <header>
            <h2>Yii3</h2>
            <p>Find the repository containing the affected package.</p>
        </header>
        <div class="report-issue-section__body">
            <a class="report-issue-action" href="https://github.com/orgs/yiisoft/repositories"><?= $githubIcon ?><span>Find the affected package</span></a>
            <p class="report-issue-section__hint">In the repository, select Security, then “Report a vulnerability.”</p>
        </div>
    </section>

    <section class="report-issue-section">
        <header>
            <h2>Yii 2</h2>
            <p>Report vulnerabilities in the core framework privately.</p>
        </header>
        <div class="report-issue-section__body">
            <a class="report-issue-action" href="https://github.com/yiisoft/yii2/security/advisories/new"><?= $githubIcon ?><span>Report a Yii 2 vulnerability</span></a>
        </div>
    </section>

    <section class="report-issue-section">
        <header>
            <h2>Yii 1.1</h2>
            <p>Report vulnerabilities in the maintained Yii 1.1 framework.</p>
        </header>
        <div class="report-issue-section__body">
            <a class="report-issue-action" href="https://github.com/yiisoft/yii/security/advisories/new"><?= $githubIcon ?><span>Report a Yii 1.1 vulnerability</span></a>
        </div>
    </section>

    <section class="report-issue-section">
        <header>
            <h2>Website</h2>
            <p>Report vulnerabilities in yiiframework.com or its accounts.</p>
        </header>
        <div class="report-issue-section__body">
            <a class="report-issue-action" href="https://github.com/yiisoft-contrib/yiiframework.com/security/advisories/new"><?= $githubIcon ?><span>Report a website vulnerability</span></a>
        </div>
    </section>

    <p class="security-report-page__guidance">
        Include affected versions, reproduction steps, impact, and a suggested fix when available. GitHub reporting
        requires an account. As a non-commercial open-source project, Yii cannot offer bug bounties.
    </p>
</main>
