<?php

/** @var $model app\models\Extension the data model */
use yii\helpers\Html;

/** @var $key mixed the key value associated with the data item */
/** @var $index integer the zero-based index of the data item in the items array returned by dataProvider. */
/** @var $widget yii\widgets\ListView the widget instance */

?>
<div class="row">
    <div class="col-md-12 col-lg-9">
        <div class="content extension-row">
            <div class="suptitle">
                Created <?= Yii::$app->formatter->asRelativeTime($model->created_at) ?> by <?= $model->owner->rankLink ?>.
            </div>
            <h2 class="title"><?= Html::a(Html::encode($model->name), ['extension/view', 'id' => $model->id]) ?></h2>
            <div class="text"><?php /*= $model->teaser*/ ?></div>
            <div class="comments"><?= Html::a(
                    Yii::$app->i18n->format('{n, number} {n, plural, one{comment} other{comments}}', ['n' => $model->comment_count], Yii::$app->language),
                    ['extension/view', 'id' => $model->id, '#' => 'comments']
            ) ?></div>
        </div>
    </div>
    <div class="col-md-12 col-lg-3">
        <?= $this->render('_metadata.php', ['model' => $model, 'extended' => false]) ?>
    </div>
</div>


