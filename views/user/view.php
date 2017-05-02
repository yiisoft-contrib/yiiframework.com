<?php

use app\models\Badge;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'User: ' . $model->username;

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

        <div class="forum-link">
       		<?= Html::a('&raquo; View forum profile',$model->forumUrl); ?>
       	</div>

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

                'post_count',
                'extension_count',
                'wiki_count',
                'comment_count',

            ],
        ]) ?>


        <h2>Wiki Articles</h2>

        <ul>
            <?php foreach($model->getWikis()->orderBy('title')->active()->all() as $wiki) {
                echo "<li>[Wiki] " . Html::a(Html::encode($wiki->title), ['wiki/view', 'id' => $wiki->id, 'name' => $wiki->slug]) . '</li>';
            } ?>
        </ul>

        <h2>Extensions</h2>

        <ul>
            <?php foreach($model->getExtensions()->orderBy('name')->active()->all() as $extension) {
                echo "<li>[Extension] " . Html::a(Html::encode($extension->name), ['extension/view', 'name' => $extension->name]) . '</li>';
            } ?>
        </ul>

       	<?php /*if($model->extension_count>0): ?>
       	<h2>Extensions</h2>
       	<ul class="g-list-none">
       		<?php foreach($extensions=$model->extensions as $extension): ?>
       		<li>&raquo; <?php echo l(h($extension->name), $extension->url); ?></li>
       		<?php endforeach; ?>
       	</ul>
       	<?php endif; ?>

       	<?php if($model->wiki_count>0): ?>
       	<h2>Wiki Articles</h2>
       	<ul class="g-list-none">
       		<?php foreach($wikis=$model->wikis as $wiki): ?>
       		<li>&raquo; <?php echo l(h($wiki->title), $wiki->url); ?></li>
       		<?php endforeach; ?>
       	</ul>
       	<?php endif;*/ ?>

        <?php if(!empty($model->badges)): ?>

            <h2>Badges</h2>
            <ul class="g-list-none">
                <?php foreach($model->badges as $info): ?>
                <?php
                   if($info->complete_time)
                       $title = sprintf('%s earned this badge on %s', Html::encode($model->display_name), Yii::$app->formatter->asDate($info->complete_time));
                   else
                       $title = sprintf('%s started this badge on %s',Html::encode($model->display_name), Yii::$app->formatter->asDate($info->create_time));
               ?>
                   <li>
                       <div class="badge" title="<?php echo $title ?>">
                           <h3><?= Html::a(Html::encode($info->badge->name), ['user/view-badge', 'name' => $info->badge->urlname]) ?></h3>
                           <p><?= Html::encode($info->badge->description) ?></p>
                           <?php if($info->complete_time): ?>
                               <span class="completed">Earned: <span class="date"><?php echo Yii::$app->formatter->asRelativeTime($info->complete_time) ?></span></span>
                           <?php else: ?>
                           <?php $percent = min(100, $info->progress); ?>
                           <div class="g-progress clearfix">
                               <div class="bar" style="width: <?php echo round($percent) ?>%"></div>
                               <div class="info">In progress</div>
                           </div>
                           <?php endif ?>
                       </div>
                   </li>
               <?php endforeach ?>
           </ul>
           <?php endif ?>


    </div>
</div>