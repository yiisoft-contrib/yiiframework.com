<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Team';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container content">
    <h1><?= Html::encode($this->title) ?></h1>

    <section class="content" id="about-team">
        <div class="container">
            <h2>Current Developer Team</h2>

            <?php foreach($members as $member): ?>
                <?php if (!$member['active']) continue ?>
                <div class="col-sm-4">
                    <div class="profile">
                        <img src="<?= Html::encode($member['photo']) ?>" class="img-responsive center-block" alt="">
                        <h3><?= Html::encode($member['name'])?><small><?= Html::encode($member['location'])?></small></h3>
                        <h4>Since <?= Html::encode($member['memberSince']) ?></h4>
                        <p><?= Html::encode($member['duty']) ?></p>
                        <ul class="brands brands-sm brands-circle brands-inline main">
                            <li><a href="" class="brands-facebook"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="" class="brands-twitter"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="" class="brands-google-plus"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

        <div class="container">
            <h2>Past Team Members</h2>

            <?php foreach($members as $member): ?>
                <?php if ($member['active']) continue ?>
                <div class="col-sm-4">
                    <div class="profile">
                        <img src="<?= Html::encode($member['photo']) ?>" class="img-responsive center-block" alt="">
                        <h3><?= Html::encode($member['name'])?><small><?= Html::encode($member['location'])?></small></h3>
                        <h4>Since <?= Html::encode($member['memberSince']) ?></h4>
                        <p><?= Html::encode($member['duty']) ?></p>
                        <ul class="brands brands-sm brands-circle brands-inline main">
                            <li><a href="" class="brands-facebook"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="" class="brands-twitter"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="" class="brands-google-plus"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

        <div class="container">
            <h2>Contributors</h2>

            <div id="contributors">TODO: get from https://api.github.com/repos/yiisoft/yii2/contributors</div>
        </div>
    </section>
</div>
