<?php
/**
 * @var Wiki $model
 * @var WikiRevision $revision
 */

use app\components\UserPermissions;
use app\models\Wiki;
use app\models\WikiRevision;
use app\widgets\Comments;
use yii\helpers\Html;

$this->title = $model->title;

?>
<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-sm-9 col-md-10 col-lg-10" role="main">
            <div class="content wiki-row wiki-article">
                <h2 class="title"><?= Html::a(Html::encode($model->title), ['wiki/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>

                <?= $this->render('_metadata.php', [
                    'model' => $model,
                    'extended' => true,
                    'inline' => true,
                ]) ?>

                <div class="text">

                            <?php if ($model->yii_version === null && $revision === null) {
                                echo '<blockquote class="note"><p>'
                                   . "This wiki article has not been tagged with a corresponding Yii version yet.<br>\nHelp us improve the wiki by "
                                   . Html::a('updating the version information', ['wiki/update', 'id' => $model->id]) . '.</p></blockquote>';
                            } ?>
                            <?php if ($revision !== null) {
                                $previous = $revision->findPrevious();
                                $next = $revision->findNext();
                                echo '<blockquote class="note"><p>'
                                   . "You are viewing revision #" . ((int) $revision->revision) . " of this wiki article.<br>";
                                if ($revision->isLatest()) {
                                    echo "This is the latest version of this article.<br>";
                                    if ($previous !== null) {
                                        echo "You may want to " . Html::a('see the changes made in this revision', ['wiki/revision', 'id' => $model->id, 'r1' => $revision->revision]) . '.';
                                    }
                                } else {
                                    echo "This version may not be up to date with the latest version.<br>"
                                        . "You may want to " . Html::a('view the differences to the latest version', ['wiki/revision', 'id' => $model->id, 'r1' => $revision->revision, 'r2' => 'latest']);
                                    if ($previous !== null) {
                                        echo " or " . Html::a('see the changes made in this revision', ['wiki/revision', 'id' => $model->id, 'r1' => $revision->revision]);
                                    }
                                    echo '.';
                                }
                                if ($previous || $next) {
                                    echo '</p><p class="clearfix">';
                                }
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

        <aside class="col-sm-3 col-md-2 col-lg-2 wiki-sidebar wiki-article-sidebar">
            <div class="wiki-article-sidebar__tools">
                <?= Html::a(
                    'Update Article',
                    ['wiki/update', 'id' => $model->id],
                    ['class' => 'btn btn-primary']
                ) ?>

                <?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_WIKI)): ?>
                    <?= Html::a('View as Admin', ['wiki-admin/view', 'id' => $model->id]) ?>
                <?php endif; ?>
            </div>

            <h3 class="wiki-side-title">Revisions</h3>
            <?= $this->render('_revisions.php', ['model' => $model]) ?>

            <?php $related = $model->getRelatedWikis() ?>
            <?php if (!empty($related)): ?>
                <h3 class="wiki-side-title">Related Articles</h3>
                <ul class="wiki-side-menu">
                    <?php foreach ($related as $wiki): ?>
                        <li><?= Html::a(Html::encode($wiki->getLinkTitle()), $wiki->getUrl()) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </aside>
    </div>
</div>
<div class="comments-wrapper">
    <div class="container comments">
        <?= Comments::widget([
            'objectType' => $model->getObjectType(),
            'objectId' => $model->getObjectId(),
            'prompt' => 'Please only use comments to help explain the above article.<br/>If you have any questions, please ask in '.Html::a('the forum', Yii::$app->request->baseUrl . '/forum').' instead.',
        ]) ?>
    </div>
</div>
