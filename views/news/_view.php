<?php

use yii\helpers\Html;

/** @var $model app\models\News */

?>
<article class="news-list-item">
    <h2><?= Html::a(Html::encode($model->title), ['news/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>
    <time class="date"><?= Yii::$app->formatter->asDate($model->news_date) ?></time>
    <div class="text">
        <?= Yii::$app->formatter->asGuideMarkdown($model->getPreviewContent()) ?>
        <?php if ($model->isPreviewTruncated()): ?>
            <p class="news-read-more-wrap"><?= Html::a(
                'Read more<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M7.5 4.5 13 10l-5.5 5.5-1.4-1.4 4.1-4.1-4.1-4.1 1.4-1.4Z"/></svg>',
                ['news/view', 'id' => $model->id, 'name' => $model->slug],
                ['class' => 'news-read-more']
            ) ?></p>
        <?php endif; ?>
    </div>
</article>
