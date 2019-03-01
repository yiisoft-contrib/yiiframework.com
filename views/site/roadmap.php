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

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Version</th>
                        <th>Initial release</th>
                        <th>Enhancements until</th>
                        <th>Bugfixes until</th>
                        <th>Security and compatibility fixes until</th>
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
