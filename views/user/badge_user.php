<div class="user">
    <span class="date grid_2 alpha"><?= Yii::$app->formatter->asRelativeTime($model->complete_time) ?></span> to
<?php echo $model->user->rankLink ?>
</div>
