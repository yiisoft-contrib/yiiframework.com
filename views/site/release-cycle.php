<?php

use app\widgets\VersionCalendar;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $versions array */

$this->title = 'Release cycle';
$this->params['breadcrumbs'][] = $this->title;
?>

<main class="container content style_external_links release-cycle-page">
    <header class="release-cycle-page__intro">
        <h1>Release cycle</h1>
        <p>Yii 1.1, Yii 2, and Yii3 follow separate release cycles and maintenance policies.</p>
    </header>

    <div class="release-policies">
        <section class="release-policy release-policy--yii3">
            <header>
                <p class="release-policy__eyebrow">Package-based</p>
                <h2>Yii3</h2>
                <p>Each package is versioned independently using <a href="https://semver.org/">Semantic Versioning</a>.</p>
            </header>

            <dl class="release-version-types">
                <div>
                    <dt>Major</dt>
                    <dd>Released at most yearly. May include breaking changes and removes deprecated code. Migration steps are documented in <code>UPGRADE.md</code>.</dd>
                </div>
                <div>
                    <dt>Minor</dt>
                    <dd>Adds features and may deprecate APIs without removing them. Platform requirements, including supported PHP versions, may change.</dd>
                </div>
                <div>
                    <dt>Patch</dt>
                    <dd>Contains compatible bug fixes, documentation improvements, and internal refactoring. Safe to upgrade without platform requirement changes.</dd>
                </div>
            </dl>
        </section>

        <section class="release-policy">
            <header>
                <p class="release-policy__eyebrow">Framework and extensions</p>
                <h2>Yii 2</h2>
                <p>The core framework and official extensions are versioned independently.</p>
            </header>
            <ul>
                <li>The current major version receives enhancements until work begins on the next major version.</li>
                <li>The previous major version may continue to receive security fixes.</li>
            </ul>
        </section>

        <section class="release-policy">
            <header>
                <p class="release-policy__eyebrow">Maintenance mode</p>
                <h2>Yii 1.1</h2>
                <p>Updates are limited to changes that keep existing applications secure and operational.</p>
            </header>
            <ul>
                <li>Support for newer PHP versions where feasible.</li>
                <li>Security fixes.</li>
                <li>Critical bug fixes that do not require breaking changes.</li>
            </ul>
        </section>
    </div>

    <aside class="release-php-note" aria-labelledby="php-support-title">
        <div>
            <p class="release-policy__eyebrow">Platform policy</p>
            <h2 id="php-support-title">PHP version support</h2>
        </div>
        <div>
            <p>Yii is continuously tested with PHPUnit. When a <a href="https://phpunit.de/supported-versions.html">supported PHPUnit version</a> drops an old PHP version, Yii may remove that version from its test matrix.</p>
            <p>End-of-life PHP versions may be dropped in a minor release. We recommend using a <a href="https://www.php.net/supported-versions.php">currently supported PHP version</a>.</p>
        </div>
    </aside>

    <section class="release-timeline" aria-labelledby="current-versions-title">
        <header class="release-timeline__header">
            <div>
                <p class="release-policy__eyebrow">Support timeline</p>
                <h2 id="current-versions-title">Current versions</h2>
            </div>
            <p>The timeline shows the support phase for each Yii branch. Lighter colors indicate forecast dates based on a new major release today.</p>
        </header>

        <div class="release-calendar" role="img" aria-label="Timeline of current Yii versions and their support phases">
            <?= VersionCalendar::widget(['versions' => $versions]) ?>
        </div>

        <ul class="release-legend" aria-label="Timeline legend">
            <li><span class="release-legend__swatch release-legend-active"></span><strong>Active support</strong><small>Features, bug fixes, and security fixes</small></li>
            <li><span class="release-legend__swatch release-legend-freeze"></span><strong>Feature freeze</strong><small>Bug, security, and compatibility fixes</small></li>
            <li><span class="release-legend__swatch release-legend-security"></span><strong>Security support</strong><small>Security and PHP compatibility fixes</small></li>
            <li><span class="release-legend__swatch release-legend-future"></span><strong>Future</strong><small>Not released yet</small></li>
        </ul>
    </section>

    <section class="release-details" aria-labelledby="release-details-title">
        <header>
            <h2 id="release-details-title">Lifecycle dates</h2>
            <p>Dates for announced phases are listed below. Unannounced dates are calculated relative to the next major release.</p>
        </header>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Version</th>
                    <th>PHP</th>
                    <th>Release</th>
                    <th>Feature freeze</th>
                    <th>Security fixes only</th>
                    <th>End of life</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($versions as $branch => $data): ?>
                    <tr>
                        <th scope="row"><?= Html::encode($branch) ?></th>
                        <td><?= Html::encode($data['php'] ?? 'To be announced') ?></td>
                        <td>
                            <?= Html::encode(!empty($data['release-estimate'])
                                ? 'By end of ' . substr($data['release'], 0, 4) . ' (estimate)'
                                : ($data['release'] ?? 'To be announced')) ?>
                        </td>
                        <td><?= Html::encode($data['enhancements'] ?? 'To be announced') ?></td>
                        <td><?= Html::encode($data['bugfixes'] ?? 'Next release +2 years') ?></td>
                        <td><?= Html::encode($data['eol'] ?? 'Next release +5 years') ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <p class="release-details__note">Yii <a href="https://github.com/yiisoft/yii2/discussions/19831#discussioncomment-5858046">2.1 was skipped</a>.</p>
    </section>
</main>
