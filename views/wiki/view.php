<?php

use yii\helpers\Html;

/** @var $model \app\models\Wiki */


$this->title = 'Wiki - ' . $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="guide-header-wrap">
    <div class="container guide-header common-heading">
        <div class="row">
            <div class="col-md-12">
                <h1 class="guide-headline"><?= Html::encode($this->title) ?></h1>
            </div>
        </div>
    </div>
</div>

<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar') ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <!-- delete from here -->
            <div class="row">
                <div class="col-md-12 col-lg-9">
                    <div class="content wiki-row">
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


                    TODO revisions
                </div>
            </div>


        </div>
    </div>
</div>
<div class="comments-wrapper">
    <div class="container comments">
        <?= \app\widgets\Comments::widget([
            'objectType' => 'wiki',
            'objectId' => "$model->id",
        ]) ?>
    </div>
</div>
