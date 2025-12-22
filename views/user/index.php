<?php

use app\components\KeysetDataProvider;
use app\components\UserPermissions;
use yii\bootstrap\Nav;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider KeysetDataProvider */

$this->title = 'Members';

if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_USERS)) {
    $this->beginBlock('adminNav');
    echo Nav::widget([
        'id' => 'admin-nav',
        'items' => [
            ['label' => 'User Admin', 'url' => ['user-admin/index']],
        ],
    ]);
    $this->endBlock();
}

$this->registerMetaTag(['name' => 'keywords', 'value' => 'yii framework, community, members']);

/** @var \app\models\User[] $models */
$models = $dataProvider->getModels();
$pagination = $dataProvider->getPagination();

?>
<div class="container style_external_links">
    <div class="content">
        <h1>Members</h1>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>User</th>
                    <th>Member Since</th>
                    <th>Overall Rating</th>
                    <th>Extensions</th>
                    <th>Wiki Articles</th>
                    <th>Comments</th>
                    <th>Forum Posts</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $model): ?>
                <tr>
                    <td><?= $model->rank == 999999 ? 'not ranked' : Html::encode($model->rank) ?></td>
                    <td><?= $model->rankLink ?></td>
                    <td><?= Yii::$app->formatter->asDate($model->created_at) ?></td>
                    <td><?= Html::encode($model->rating) ?></td>
                    <td><?= Html::encode($model->extension_count) ?></td>
                    <td><?= Html::encode($model->wiki_count) ?></td>
                    <td><?= Html::encode($model->comment_count) ?></td>
                    <td><?= Html::encode($model->post_count) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>

        <?php if ($pagination !== false): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php if ($pagination->hasPrevPage): ?>
                    <li>
                        <?= Html::a('&laquo; Previous', $pagination->getPreviousPageUrl(), ['aria-label' => 'Previous']) ?>
                    </li>
                <?php else: ?>
                    <li class="disabled">
                        <span aria-label="Previous">&laquo; Previous</span>
                    </li>
                <?php endif; ?>

                <?php if ($pagination->hasNextPage): ?>
                    <li>
                        <?= Html::a('Next &raquo;', $pagination->getNextPageUrl(), ['aria-label' => 'Next']) ?>
                    </li>
                <?php else: ?>
                    <li class="disabled">
                        <span aria-label="Next">Next &raquo;</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

    </div>
</div>
