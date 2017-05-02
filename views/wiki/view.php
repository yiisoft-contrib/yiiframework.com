<?php

use yii\helpers\Html;

/** @var $model \app\models\Wiki */
/** @var $revision int */


$this->title = 'Wiki';
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
            <?= $this->render('_sidebar', [
                'category' => $model->category_id,
            ]) ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <div class="row">
                <div class="col-md-12 col-lg-9">
                    <div class="content wiki-row">
                        <h2 class="title"><?= Html::a(Html::encode($model->title), ['wiki/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>
                        <div class="text">

                            <?php if ($model->yii_version === null && $revision === null) {
                                echo '<blockquote class="note"><p>'
                                   . "This wiki article has not been tagged with a corresponding Yii version yet.<br>\nHelp us improve the wiki by "
                                   . Html::a('updating the version information', ['wiki/update', 'id' => $model->id]) . '.</p></blockquote>';
                            } ?>
                            <?php if ($revision !== null) {
                                echo '<blockquote class="note"><p>'
                                   . "You are viewing revision #" . ((int) $revision->revision) . " of this wiki article.<br>";
                                if ($revision->isLatest()) {
                                    echo "This is the latest version of this article.<br>"
                                        . "You may want to " . Html::a('see the changes made in this revision', ['wiki/revision', 'id' => $model->id, 'r1' => $revision->revision]) . '.';
                                } else {
                                    echo "This version may not be up to date with the latest version.<br>"
                                        . "You may want to " . Html::a('view the differences to the latest version', ['wiki/revision', 'id' => $model->id, 'r1' => $revision->revision, 'r2' => 'latest'])
                                        . " or " . Html::a('see the changes made in this revision', ['wiki/revision', 'id' => $model->id, 'r1' => $revision->revision]) . '.';
                                }
                                $previous = $revision->findPrevious();
                                $next = $revision->findNext();
                                if ($previous || $next) echo '</p><p class="clearfix">';
                                if ($previous) {
                                    echo Html::a(
                                        '&laquo; previous (#' . $previous->revision . ')',
                                        ['wiki/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $previous->revision],
                                        ['class' => 'prev-revision']
                                    );
                                }
                                if ($next) {
                                    echo Html::a(
                                        'next (#' . $next->revision . ') &raquo;',
                                        ['wiki/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $next->revision],
                                        ['class' => 'next-revision']
                                    );
                                }
                                echo '</p></blockquote>';

                            } ?>

                            <?= $model->contentHtml ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-3">
                    <?= $this->render('_metadata.php', ['model' => $model, 'extended' => true]) ?>

                    <?= Html::a('Update Article', ['wiki/update', 'id' => $model->id])?>


                    <h3>Revisions</h3>

                    <?= $this->render('_revisions.php', ['model' => $model]) ?>
                </div>
            </div>


        </div>
    </div>
</div>
<div class="comments-wrapper">
    <div class="container comments">
        <?= \app\widgets\Comments::widget([
            'objectType' => \app\models\Wiki::COMMENT_TYPE,
            'objectId' => "$model->id",
            'prompt' => 'Please only use comments to help explain the above article.<br/>If you have any questions, please ask in '.Html::a('the forum', Yii::$app->request->baseUrl . '/forum').' instead.',
        ]) ?>
    </div>
</div>
