<?php
/**
 * @var Extension $model
 * @var int $revision
 */

use app\models\Extension;
use yii\helpers\Html;

$this->title = $model->name;

?>
<div class="container guide-view lang-en">
    <div class="row">
        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar', [
                'category' => $model->category_id,
            ]) ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <?= \app\widgets\Alert::widget() ?>

            <div class="row">
                <div class="col-md-12 col-lg-9">
                    <div class="content extension-row">
                        <h2 class="title"><?= Html::encode($model->name) ?> <small><?= Html::encode($model->tagline) ?></small></h2>
                        <div class="text">

                            <?php /*if ($model->yii_version === null && $revision === null) {
                                echo '<blockquote class="note"><p>'
                                   . "This extension article has not been tagged with a corresponding Yii version yet.<br>\nHelp us improve the extension by "
                                   . Html::a('updating the version information', ['extension/update', 'id' => $model->id]) . '.</p></blockquote>';
                            } ?>
                            <?php if ($revision !== null) {
                                echo '<blockquote class="note"><p>'
                                   . "You are viewing revision #" . ((int) $revision->revision) . " of this extension article.<br>";
                                if ($revision->isLatest()) {
                                    echo "This is the latest version of this article.<br>"
                                        . "You may want to " . Html::a('see the changes made in this revision', ['extension/revision', 'id' => $model->id, 'r1' => $revision->revision]) . '.';
                                } else {
                                    echo "This version may not be up to date with the latest version.<br>"
                                        . "You may want to " . Html::a('view the differences to the latest version', ['extension/revision', 'id' => $model->id, 'r1' => $revision->revision, 'r2' => 'latest'])
                                        . " or " . Html::a('see the changes made in this revision', ['extension/revision', 'id' => $model->id, 'r1' => $revision->revision]) . '.';
                                }
                                $previous = $revision->findPrevious();
                                $next = $revision->findNext();
                                if ($previous || $next) echo '</p><p class="clearfix">';
                                if ($previous) {
                                    echo Html::a(
                                        '&laquo; previous (#' . $previous->revision . ')',
                                        ['extension/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $previous->revision],
                                        ['class' => 'prev-revision']
                                    );
                                }
                                if ($next) {
                                    echo Html::a(
                                        'next (#' . $next->revision . ') &raquo;',
                                        ['extension/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $next->revision],
                                        ['class' => 'next-revision']
                                    );
                                }
                                echo '</p></blockquote>';

                            }*/ ?>

                            <?php $html = $model->contentHtml;
                            if ($model->from_packagist && $model->update_status == Extension::UPDATE_STATUS_NEW) {
                                $this->registerJs(<<<JS
                                    setTimeout(function () {
                                        location.reload();
                                    }, 3000);
JS
                                );
                                echo '<div class="packagist-spinner"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i><br>updating data from Packagist...</div>';
                            } else {
                                echo $html;
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-3">

                    <?= $this->render('_info.php', ['model' => $model]) ?>

                </div>
            </div>


        </div>
    </div>
</div>
<div class="comments-wrapper">
    <div class="container comments">
        <?= \app\widgets\Comments::widget([
            'objectType' => $model->getObjectType(),
            'objectId' => $model->getObjectId(),
            'prompt' => 'Please only use comments to help explain the above extension.<br/>If you have any questions, please ask in '.Html::a('the forum', Yii::$app->request->baseUrl . '/forum').' instead.',
        ]) ?>
    </div>
</div>
