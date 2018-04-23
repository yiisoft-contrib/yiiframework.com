<?php
use yii\helpers\Html;

/** @var array $data */
?>

<div class="container">
        <div class="content">
            <table class="table">
                <thead>
                    <tr>
                        <th>Repository</th>
                        <th>Latest</th>
                        <th>No release for</th>
                        <th>Status</th>
                        <th>Diff</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                        <?php if ($row === null): ?>
                            <td colspan="5">No releases yet.</td>
                        <?php else: ?>
                            <td><?= Html::a(Html::encode($row['repository']), 'https://github.com/' . $row['repository']) ?></td>
                            <td><?= Html::encode($row['release']) ?></td>
                            <td><?= Html::encode($row['days_since']) ?> day<?= (int)$row['days_since'] === 1 ? '' : 's' ?></td>
                            <td><?= Html::img($row['status_url']) ?></td>
                            <td><?= Html::a('Check', $row['diff_url']) ?></td>
                        <?php endif ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
</div>