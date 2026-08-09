<?php

use app\models\Wiki;
use app\models\WikiRevision;
use yii\helpers\Html;

/** @var Wiki $model */
/** @var WikiRevision $left */
/** @var WikiRevision $right */
/** @var WikiRevision $diffSingle */


$this->title = $model->title . ' | Compare Revisions';

?>
<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-xs-12" role="main">
            <div class="content wiki-row wiki-revision-page">
                <h2 class="title"><?= Html::a(Html::encode($model->title), ['wiki/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>
                <p class="wiki-revision-page__summary">
                    Comparing
                    <?= Html::a('#' . Html::encode($left->revision), ['wiki/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $left->revision]) ?>
                    with
                    <?= Html::a('#' . Html::encode($right->revision), ['wiki/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $right->revision]) ?>
                </p>

                <?php if ($left->equals($right)): ?>
                    <div class="alert alert-warning">A revision cannot be compared with itself.</div>
                <?php else: ?>
                    <?php if ($diffSingle): ?>
                        <div class="wiki-revision-page__details">
                            <p>
                                Revision #<?= $diffSingle->revision ?> was created by <?= $diffSingle->updater->rankLink ?>
                                on <?= Yii::$app->formatter->asDateTime($diffSingle->updated_at) ?>.
                            </p>
                            <?php if ($right->memo): ?>
                                <div class="memo"><?= Html::encode($right->memo) ?></div>
                            <?php endif; ?>

                            <nav class="wiki-revision-page__navigation" aria-label="Revision navigation">
                                <?php if ($previous = $diffSingle->findPrevious()): ?>
                                    <?= Html::a(
                                        '&laquo; Previous (#' . $previous->revision . ')',
                                        ['wiki/revision', 'id' => $previous->wiki_id, 'r1' => $previous->revision]
                                    ) ?>
                                <?php endif; ?>
                                <?php if ($next = $diffSingle->findNext()): ?>
                                    <?= Html::a(
                                        'Next (#' . $next->revision . ') &raquo;',
                                        ['wiki/revision', 'id' => $next->wiki_id, 'r1' => $next->revision]
                                    ) ?>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>

                    <?= $this->render('_changes', [
                        'left' => $left,
                        'right' => $right,
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
