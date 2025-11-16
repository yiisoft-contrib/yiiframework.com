<?php
use app\models\UserBadge;

/** @var UserBadge $model */
?>
<div class="user">
    <span class="date grid_2 alpha"><?= Yii::$app->formatter->asRelativeTime($model->complete_time) ?></span> to
<?= $model->user->rankLink ?>
</div>
