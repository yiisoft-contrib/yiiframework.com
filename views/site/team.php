<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $activeMembers array */
/* @var $pastMembers array */
/* @var $contributors array */
$this->title = 'Team';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container content">
    <h1><?= Html::encode($this->title) ?></h1>

    <section class="content" id="about-team">
        <div class="container">
            <h2>Current Developer Team</h2>

            <?php foreach($activeMembers as $row): ?>
                <div class="row">
                    <?php foreach($row as $member):?>
                        <div class="col-sm-4">
                            <div class="profile">
                                <img src="<?= Html::encode(Yii::getAlias($member['photo'])) ?>" class="img-responsive center-block" alt="">
                                <h3><?= Html::encode($member['name'])?><small><?= Html::encode($member['location'])?></small></h3>
                                <h4>Since <?= Html::encode($member['memberSince']) ?></h4>
                                <p class="duty"><?= Html::encode($member['duty']) ?></p>
                                <ul class="brands brands-inline brands-sm brands-transition brands-circle">
                                    <?php
                                    if (isset($member['github'])) {
                                        echo '<li>' . Html::a('<i class="fa fa-github"></i>', 'https://github.com/' . $member['github'], ['class' => 'brands-github']) . '</li>';
                                    }
                                    if (isset($member['twitter'])) {
                                        echo '<li>' . Html::a('<i class="fa fa-twitter"></i>', 'https://twitter.com/' . $member['twitter'], ['class' => 'brands-twitter']) . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endforeach ?>
        </div>

        <div class="container">
            <h2>Past Team Members</h2>

            <?php foreach($pastMembers as $row): ?>
            <div class="row">
                <?php foreach($row as $member):?>
                    <div class="col-sm-4">
                        <div class="profile">
                            <img src="<?= Html::encode(Yii::getAlias($member['photo'])) ?>" class="img-responsive center-block" alt="">
                            <h3><?= Html::encode($member['name'])?><small><?= Html::encode($member['location'])?></small></h3>
                            <h4><?= Html::encode($member['memberSince']) ?></h4>
                            <p class="duty"><?= Html::encode($member['duty']) ?></p>
                            <ul class="brands brands-inline brands-sm brands-transition brands-circle">
                                <?php
                                if (isset($member['github'])) {
                                    echo '<li>' . Html::a('<i class="fa fa-github"></i>', 'https://github.com/' . $member['github'], ['class' => 'brands-github']) . '</li>';
                                }
                                if (isset($member['twitter'])) {
                                    echo '<li>' . Html::a('<i class="fa fa-twitter"></i>', 'https://twitter.com/' . $member['twitter'], ['class' => 'brands-twitter']) . '</li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>
        </div>

        <div class="container">
            <h2>Contributors</h2>

            <div id="contributors">
                <?php foreach ($contributors as $contributor) {
                    echo Html::a(Html::img($contributor['avatar_url'] . '&s=42', ['alt' => 'Avatar of ' . $contributor['login']]), $contributor['html_url'], ['title' => $contributor['login'] . ' on Github']);
                } ?>
            </div>
        </div>
    </section>
</div>
