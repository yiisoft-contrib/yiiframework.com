<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $activeMembers array */
/* @var $pastMembers array */
/* @var $inactiveMembers array */
/* @var $contributors array */
$this->title = 'Team';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container site-header">
    <div class="row">
        <div class="col-md-6">
            <h1>Team</h1>
            <h2>Current & Past Team Members</h2>
        </div>
        <div class="col-md-6">
            <img class="background" src="<?= Yii::getAlias('@web/image/team/team.svg')?>" alt="">
        </div>
    </div>
</div>

<div class="container">
        <div class="content">
            <div class="col-md-12">
                <div class="heading-separator">
                    <h2><span>Current Developer Team</span></h2>
                </div>

                <?php foreach($activeMembers as $row): ?>
                    <div class="row">
                        <?php foreach($row as $i=>$member):?>
                            <div class="col-sm-4 col-md-2 person-card <?= ($i==0 && count($row)<6)?'col-md-offset-'.(6-count($row)):'' ?>">
                                    <div class="avatar">
                                        <img src="<?= Html::encode(Yii::getAlias($member['photo'] ?? '@web/image/team/noimage.png')) ?>" class="img-responsive" alt="" />
                                    </div>
                                    <div class="team-content">
                                        <ul class="links-inline links-circle">
                                            <?php

                                            if (isset($member['twitter'])) {
                                                echo '<li class="twitter">' . Html::a('<i class="fa fa-twitter">
                                                    </i>', 'https://twitter.com/' . $member['twitter'], ['title' => $member['name'].' on Twitter']) . '</li>';
                                            }
                                            if (isset($member['github'])) {
                                                echo '<li class="github">' . Html::a('<i class="fa fa-github">
                                                    </i>', 'https://github.com/' . $member['github'], ['title' => $member['name'].' on Github']) . '</li>';
                                            }
                                            ?>
                                        </ul>
                                        <h4><?= Html::encode($member['name'])?></h4>
                                        <p class="duty"><?= HtmlPurifier::process($member['duty']) ?></p>
                                        <p class="location"> <?= Html::encode($member['location'])?></p>
                                        <p class="period"><?= Html::encode($member['period']) ?></p>


                                    </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endforeach ?>

                <div class="heading-separator">
                    <h2><span>Inactive Team Members</span></h2>
                </div>

                <?php foreach($inactiveMembers as $row): ?>
                <div class="row">
                    <?php foreach($row as $i=>$member):?>
                        <div class="col-sm-4 col-md-2 person-card <?= ($i==0 && count($row)<6)?'col-md-offset-'.(6-count($row)):'' ?>">
                            <div class="team-card">
                                <div class="avatar">
                                    <img src="<?= Html::encode(Yii::getAlias($member['photo'])) ?>" class="img-responsive" alt="" />
                                </div>
                                <div class="team-content">
                                    <ul class="links-inline links-circle">
                                            <?php

                                            if (isset($member['twitter'])) {
                                                echo '<li class="twitter">' . Html::a('<i class="fa fa-twitter">
                                                    </i>', 'https://twitter.com/' . $member['twitter'], ['title' => $member['name'].' on Twitter']) . '</li>';
                                            }
                                            if (isset($member['github'])) {
                                                echo '<li class="github">' . Html::a('<i class="fa fa-github">
                                                    </i>', 'https://github.com/' . $member['github'], ['title' => $member['name'].' on Github']) . '</li>';
                                            }
                                            ?>
                                        </ul>
                                        <h4><?= Html::encode($member['name'])?></h4>
                                        <p class="duty"><?= HtmlPurifier::process($member['duty']) ?></p>
                                        <p class="location"> <?= Html::encode($member['location'])?></p>
                                        <p class="period"><?= Html::encode($member['period']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <?php endforeach ?>


                <div class="heading-separator">
                    <h2><span>Past Team Members</span></h2>
                </div>

                <?php foreach($pastMembers as $row): ?>
                <div class="row">
                    <?php foreach($row as $i=>$member):?>
                        <div class="col-sm-4 col-md-2 person-card <?= ($i==0 && count($row)<6)?'col-md-offset-'.(6-count($row)):'' ?>">
                            <div class="team-card">
                                <div class="avatar">
                                    <img src="<?= Html::encode(Yii::getAlias($member['photo'])) ?>" class="img-responsive" alt="" />
                                </div>
                                <div class="team-content">
                                    <ul class="links-inline links-circle">
                                            <?php

                                            if (isset($member['twitter'])) {
                                                echo '<li class="twitter">' . Html::a('<i class="fa fa-twitter">
                                                    </i>', 'https://twitter.com/' . $member['twitter'], ['title' => $member['name'].' on Twitter']) . '</li>';
                                            }
                                            if (isset($member['github'])) {
                                                echo '<li class="github">' . Html::a('<i class="fa fa-github">
                                                    </i>', 'https://github.com/' . $member['github'], ['title' => $member['name'].' on Github']) . '</li>';
                                            }
                                            ?>
                                        </ul>
                                        <h4><?= Html::encode($member['name'])?></h4>
                                        <p class="duty"><?= HtmlPurifier::process($member['duty']) ?></p>
                                        <p class="location"> <?= Html::encode($member['location'])?></p>
                                        <p class="period"><?= Html::encode($member['period']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <?php endforeach ?>


                <div class="heading-separator">
                    <h2><span>Contributors</span></h2>
                </div>

                <p>
                    There is a huge community of <a href="https://github.com/yiisoft/yii2/graphs/contributors">contributors</a> working on the Yii Framework code.
                    Without their help it would not be possible to provide and maintain the huge amount of functionality,
                    documentation, and translations.
                </p>

                <?php if ($contributors === false): ?>
                <div class="alert alert-warning">
                    <p>
                        Github.com seems to be unavailable. Thus we curently can not show the list of contributors here. Please try again later.
                    </p>
                </div>
                <?php else:  ?>
                <p>
                    The following list shows all the people who have contributed to the <a href="https://github.com/yiisoft/yii2">yiisoft/yii2</a> repository on Github.
                    If you are one of them, thank you! If not, <?= Html::a('become a part of it', ['site/contribute']) ?>!
                </p>

                <div id="contributors">
                    <?php foreach ($contributors as $contributor) {
                        echo Html::a(Html::img('', ['style' => 'display:inline-block;', 'class' => 'icon-'. $contributor['login']]), $contributor['html_url'], ['title' => $contributor['login'] . ' on Github']);
                    } ?>
                </div>
                <?php endif; ?>

                <p>&nbsp;</p>
            </div>
        </div>
</div>
