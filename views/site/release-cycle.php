<?php

use app\widgets\VersionCalendar;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $versions array */
?>
<div class="container style_external_links">
    <div class="content">
        <div>
            <h2>Maintenance policy</h2>

            <p>Major version is receiving enhancements until it is decided to start working on next major version.</p>

            <p>After current major version release previous major version has:</p>

            <ul>
                <li>One year of bug fixes.</li>
                <li>Three years of security and PHP compatibility fixes.</li>
            </ul>

            <p>Above policy periods may be extended for individual versions.</p>

            <h2>Current versions</h2>

            <?= VersionCalendar::widget(['versions' => $versions]) ?>

            <h3>Legend</h3>

            <table class="table">
                <tbody>
                <tr>
                    <td style="background: #9c9; width: 40px;"></td>
                    <td style="background: #9c9; width: 40px; opacity: 0.3"></td>
                    <td>Active support. Enahcements, bug fixes, security fixes are accepted.</td>
                </tr>
                <tr>
                    <td style="background: #71bdff; width: 40px;"></td>
                    <td style="background: #71bdff; width: 40px; opacity: 0.3;"></td>
                    <td>Feature freeze. Enahncements are no longer accepted.</td>
                </tr>
                <tr>
                    <td style="background: #ffb95e; width: 40px;"></td>
                    <td style="background: #ffb95e; width: 40px; opacity: 0.3;"></td>
                    <td>Security and PHP compatibility fixes only.</td>
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

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Version</th>
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
                        <td><?= Html::encode($data['release'] ?? 'To be announced') ?></td>
                        <td><?= Html::encode($data['enhancements'] ?? 'To be announced') ?></td>
                        <td><?= Html::encode($data['bugfixes'] ?? 'Next release +1 year') ?></td>
                        <td><?= Html::encode($data['eol'] ?? 'Next release +3 years') ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
