<?php

/** @var $model app\models\Wiki the data model */
use yii\helpers\Html;

/** @var $key mixed the key value associated with the data item */
/** @var $index integer the zero-based index of the data item in the items array returned by dataProvider. */
/** @var $widget yii\widgets\ListView the widget instance */

?>
<div class="row">
    <div class="col-md-12 col-lg-9">
        <div class="content wiki-row">
            <div class="suptitle">Created 17 days ago by <?= $model->creator->rankLink ?></div>
            <h2 class="title"><?= Html::a(Html::encode($model->title), ['wiki/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>
            <div class="text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
            <div class="comments"><a href="#">0 comments</a></div>
        </div>
    </div>
    <div class="col-md-12 col-lg-3">
        <?= $this->render('_metadata.php', ['model' => $model]) ?>
    </div>
</div>


