<?php

use app\widgets\VersionCalendar;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $versions array */
?>
<div class="container style_external_links">
    <div class="content">
        <div>
            <p>Yii 1.1, Yii 2, and Yii3 are separate projects with their own separate release cycles and maintenance
               policies.</p>

            <h2>Yii3</h2>

            <p>Yii3 consists of many packages. Each package is <a href="https://semver.org/">versioned using
               Semantic Versioning (SemVer)</a> independently of the others and has its own release cycle.</p>

            <ul>
                <li>
                    Major version
                    <ul>
                        <li>Released at most once per year.</li>
                        <li>May introduce backward incompatible changes (BC breaks).</li>
                        <li>Removes code that was previously deprecated in minor releases.</li>
                        <li>A documented upgrade path is provided in the package <code>UPGRADE.md</code> file.</li>
                    </ul>
                </li>
                <li>
                    Minor version
                    <ul>
                        <li>Released when a set of features or enhancements is ready.</li>
                        <li>May introduce new functionality and may mark existing APIs as deprecated, but does not remove them.</li>
                        <li>Backwards compatibility is preserved.</li>
                        <li>It is safe to upgrade within the same major version.</li>
                        <li>May adjust requirements, including the minimum or maximum supported PHP version.</li>
                        <li>
                            The supported PHP version range is stated explicitly in <code>composer.json</code> and is
                            extended only after compatibility has been verified.
                        </li>
                    </ul>
                </li>
                <li>
                    Patch version
                    <ul>
                        <li>Released when bug fixes, documentation improvements, or internal refactorings are ready.</li>
                        <li>May fix bugs and security issues.</li>
                        <li>Does not change platform requirements (for example, PHP version constraints remain the same).</li>
                        <li>Does not introduce new public features or remove existing ones.</li>
                        <li>It is safe to upgrade.</li>
                    </ul>
                </li>
            </ul>

            <h2>Yii 2</h2>

            <p>
                Yii 2 consists of the core framework and a set of official extensions.
                The framework and each official extension are versioned independently.
            </p>

            <ul>
                <li>Major version is receiving enhancements until it is decided to start working on next major version.</li>
                <li>Previous major version may receive security fixes.</li>
            </ul>

            <h2>Yii 1.1</h2>

            <p>
                Yii 1.1 is in maintenance mode. It only receives updates for:
            </p>

            <ul>
                <li>Support for newer PHP versions where feasible.</li>
                <li>Security fixes.</li>
                <li>Critical bug fixes that do not require breaking changes.</li>
            </ul>

            <h2>PHP versions support</h2>

            <p>
                To ensure the framework works correctly across supported PHP versions, we run automated tests using PHPUnit.
                If a <a href="https://phpunit.de/supported-versions.html">newer version of PHPUnit no longer supports an old PHP version</a>,
                we may remove that PHP version from our test matrix.
            </p>

            <p>
                This does not immediately mean Yii will stop working on that PHP version, but the lack of automated testing
                significantly increases the risk of regressions. Over time, new releases may no longer be compatible.
            </p>

            <p>We generally do not support PHP versions that have reached their end of life.
               When a PHP version becomes unsupported upstream, it may be removed from our supported range
               in the next minor release of a package.</p>

            <p>
                Therefore, we recommend using a
                <a href="https://www.php.net/supported-versions.php">currently supported version of PHP</a>.
            </p>

            <h2>Current versions</h2>

            <?= VersionCalendar::widget(['versions' => $versions]) ?>

            <h3>Legend</h3>

            <table class="table">
                <tbody>
                <tr>
                    <td style="background: #9c9; width: 40px;"></td>
                    <td style="background: #9c9; width: 40px; opacity: 0.3"></td>
                    <td>Active support. New features, enhancements, bug fixes, security fixes are accepted.</td>
                </tr>
                <tr>
                    <td style="background: #71bdff; width: 40px;"></td>
                    <td style="background: #71bdff; width: 40px; opacity: 0.3;"></td>
                    <td>Feature freeze. New features are no longer accepted; only bug fixes, security fixes, and critical compatibility fixes are.</td>
                </tr>
                <tr>
                    <td style="background: #ffb95e; width: 40px;"></td>
                    <td style="background: #ffb95e; width: 40px; opacity: 0.3;"></td>
                    <td>Mainly security and PHP compatibility fixes.</td>
                </tr>
                <tr>
                    <td style="background: #eee; width: 40px;"></td>
                    <td></td>
                    <td>Future version.</td>
                </tr>
                </tbody>
            </table>

            <p>Pale color means forecast in case next major version is released today.</p>

            <h2>Details</h2>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Version</th>
                        <th>PHP Version</th>
                        <th>Release (active support)</th>
                        <th>Feature freeze</th>
                        <th>Security and PHP compatibility fixes only</th>
                        <th>End of life</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($versions as $branch => $data): ?>
                        <tr>
                            <td><?= Html::encode($branch) ?></td>
                            <td><?= Html::encode($data['php'] ?? 'To be announced') ?></td>
                            <td><?= Html::encode($data['release'] ?? 'To be announced') ?></td>
                            <td><?= Html::encode($data['enhancements'] ?? 'To be announced') ?></td>
                            <td><?= Html::encode($data['bugfixes'] ?? 'Next release +2 years') ?></td>
                            <td><?= Html::encode($data['eol'] ?? 'Next release +5 years') ?></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            ¹ Note: Yii <a href="https://github.com/yiisoft/yii2/discussions/19831#discussioncomment-5858046" target="_blank">2.1</a> was skipped.
        </div>
    </div>
</div>
