<?php

/** @var $model app\models\Extension the data model */
use yii\helpers\Html;

/** @var $key mixed the key value associated with the data item */
/** @var $index integer the zero-based index of the data item in the items array returned by dataProvider. */
/** @var $widget yii\widgets\ListView the widget instance */

?>
    <div class="extension-box">
        <h2 class="title"><?= Html::a(Html::encode($model->name), $model->url) ?></h2>
        <div class="extension-stats">
            <?= Html::encode($model->category->name) ?>

            <?= Html::a(
                '<i class="fa fa-comments-o"></i> ' . $model->comment_count,
                $model->getUrl('view', ['#' => 'comments']),
                [
                    'aria-label' => $model->comment_count.' Comments',
                    'title' => $model->comment_count.' Comments',
                ]
            ) ?>

            <?= Html::a(
                '<i class="fa fa-download"></i> ' . $model->download_count,
                $model->getUrl('view', ['#' => 'downloads']),
                [
                    'aria-label' => $model->download_count.' Downloads',
                    'title' => $model->download_count.' Downloads',
                ]
            ) ?>
        </div>

        <div class="extension-tagline">
            <?= Html::encode($model->tagline) ?>
        </div>

        <div class="extension-author">
            Created by <?= $model->owner->rankLink ?>,
            <?php
            $created = Yii::$app->formatter->asRelativeTime($model->created_at);
            $updated = Yii::$app->formatter->asRelativeTime($model->updated_at);
            if ($created !== $updated) {
                echo "$created, updated $updated.";
            } else {
                echo "$created.";
            }
            ?>
        </div>

    </div>


