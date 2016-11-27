<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $model app\models\Wiki the data model */

?>

<?= Html::a('View all history', ['wiki/history', 'id' => $model->id/*, 'name' => $model->slug*/]) ?>

<ul>
<?php foreach($model->latestRevisions as $revision): ?>
    <li>
        <?= Html::a(Yii::$app->formatter->asRelativeTime($revision->updated_at), ['wiki/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $revision->revision]) ?>
        by
        <?= $revision->updater->getRankLink() ?>
        <div class="wiki-revision-memo"><?= Html::encode(StringHelper::truncate($revision->memo, 40)) ?></div>
    </li>
<?php endforeach; ?>
</ul>