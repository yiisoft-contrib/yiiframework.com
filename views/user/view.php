<?php

use app\models\Badge;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $userCount int */
/* @var $wikis app\models\Wiki[] */
/* @var $extensions app\models\Extension[] */

$this->title = $model->username . ' profile';

echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
    'menu' => [
        ['label' => 'User Admin', 'url' => ['user-admin/index'], 'visible' => Yii::$app->user->can('users:pAdmin') ],
        ['label' => 'Update User', 'url' => ['user-admin/view', 'id' => $model->id], 'visible' => Yii::$app->user->can('users:pAdmin') ],
    ]
]);

$this->registerMetaTag(['name' => 'keywords', 'value' => 'yii framework, community, members']);

?>
<div class="container style_external_links">
    <div class="content">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => 'Overall Rating',
                    'value' => "<strong>" . ((int)$model->rating) . "</strong> ("
                        . ($model->rank==999999 ? 'not ranked' :
                            Html::a('ranked as <i>No. '.((int)$model->rank).'</i> among '. ((int)$userCount) . ' members',
                               		['user/index', 'sort'=>'rank', 'page'=>((int)(($model->rank-1)/50))+1]
                            )) . ')',
                    'format' => 'raw'
                ],
                'username',
                'created_at:datetime',

                'post_count' => [
                    'label' => 'Forum Posts',
                    'value' => $model->post_count . ' (' . Html::a('view forum profile',$model->forumUrl) . ')',
                    'format' => 'raw',
                ],
                'extension_count',
                'wiki_count',
                'comment_count',

            ],
        ]) ?>


        <?php if (!empty($wikis)): ?>
            <h2>Wiki Articles</h2>

            <ul>
                <?php foreach($wikis as $wiki) {
                    echo "<li>" . Html::a(Html::encode($wiki->getLinkTitle()), $wiki->getUrl()) . '</li>';
                } ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($extensions)): ?>
            <h2>Extensions</h2>

            <ul>
                <?php foreach($extensions as $extension) {
                    echo "<li>" . Html::a(Html::encode($extension->getLinkTitle()), $extension->getUrl()) . '</li>';
                } ?>
            </ul>
        <?php endif; ?>

        <?php if(!empty($model->badges)): ?>

            <h2>Badges</h2>
            <ul class="g-list-none">
                <?php foreach($model->getBadges()->with('badge')->all() as $info): ?>
                <?php
                   if($info->complete_time)
                       $title = sprintf('%s earned this badge on %s', Html::encode($model->display_name), Yii::$app->formatter->asDate($info->complete_time));
                   else
                       $title = sprintf('%s started this badge on %s',Html::encode($model->display_name), Yii::$app->formatter->asDate($info->create_time));
               ?>
                   <li>
                       <div class="userbadge userbadge-<?php echo $info->badge->urlname ?>" title="<?php echo $title ?>">
                           <?php $percent = min(100, $info->progress); ?>
                           <div class="userbadge-progress-bar" style="width: <?php echo round($percent) ?>%"></div>
                           <div class="userbadge-info">
                               <h3><?= Html::a(Html::encode($info->badge->name), ['user/view-badge', 'name' => $info->badge->urlname]) ?></h3>
                               <p><?= Html::encode($info->badge->description) ?></p>
                               <?php if($info->complete_time): ?>
                                   <span class="percent">Earned: <span class="date"><?php echo Yii::$app->formatter->asRelativeTime($info->complete_time) ?></span></span>
                               <?php else: ?>
                                   <span class="percent">In progress (<?= round($percent)?>%)</span>
                               <?php endif ?>
                           </div>
                       </div>
                   </li>
               <?php endforeach ?>
           </ul>
           <?php endif ?>


    </div>
</div>