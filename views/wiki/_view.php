<?php

use yii\helpers\Html;

/** @var $model app\models\Wiki */
?>
<article class="wiki-list-item">
    <div class="wiki-row">
        <h2 class="title"><?= Html::a(
            Html::encode($model->title),
            ['wiki/view', 'id' => $model->id, 'name' => $model->slug]
        ) ?></h2>

        <div class="subtitle">
            Created <?= Yii::$app->formatter->asRelativeTime($model->created_at) ?>
            by <?= $model->creator->rankLink ?><?php if ($model->updated_at !== null): ?>,
                updated <?= Yii::$app->formatter->asRelativeTime($model->updated_at) ?>
                by <?= $model->updater->rankLink ?><?php endif; ?>.
            <span class="comments"><?= Html::a(
                Yii::$app->i18n->format(
                    '{n, number} {n, plural, one{comment} other{comments}}',
                    ['n' => $model->comment_count],
                    Yii::$app->language
                ),
                ['wiki/view', 'id' => $model->id, 'name' => $model->slug, '#' => 'comments']
            ) ?></span>
        </div>

        <div class="text"><?= $model->getTeaser() ?></div>

        <?= $this->render('_metadata.php', ['model' => $model, 'extended' => false]) ?>
    </div>
</article>
