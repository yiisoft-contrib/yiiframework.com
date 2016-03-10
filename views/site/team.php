<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $activeMembers array */
/* @var $pastMembers array */
/* @var $contributors array */
$this->title = 'Team';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="row">
        <div>
            <section id="about-team">
                <div class="container">
                    <div class="row">
                        <h2>Current Developer Team</h2>
                    </div>
                    <?php foreach($activeMembers as $row): ?>
                        <div class="row">
                            <?php foreach($row as $member):?>
                                <div class="col-sm-6 col-md-4">
                                    <div class="team-card">
                                        <canvas class="header-bg" width="250" height="70" id="header-blur"></canvas>
                                        <div class="avatar">
                                            <img src="<?= Html::encode(Yii::getAlias($member['photo'])) ?>" alt="">
                                        </div>
                                        <div class="team-content">
                                            <h3><?= Html::encode($member['name'])?><small> <?= Html::encode($member['location'])?></small></h3>
                                            <h4>Since <?= Html::encode($member['memberSince']) ?></h4>
                                            <p class="duty"><?= HtmlPurifier::process($member['duty']) ?></p>
                                            <ul class="links-inline links-circle">
                                                <?php
                                                if (isset($member['github'])) {
                                                    echo '<li class="github">' . Html::a('<i class="fa fa-github"></i>', 'https://github.com/' . $member['github'], ['title' => $member['name'].' on Github']) . '</li>';
                                                }
                                                if (isset($member['twitter'])) {
                                                    echo '<li class="twitter">' . Html::a('<i class="fa fa-twitter"></i>', 'https://twitter.com/' . $member['twitter'], ['title' => $member['name'].' on Twitter']) . '</li>';
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="container">
                    <div class="row">
                        <h2>Past Team Members</h2>
                    </div>
                    <?php foreach($pastMembers as $row): ?>
                    <div class="row">
                        <?php foreach($row as $member):?>
                            <div class="col-sm-6 col-md-4">
                                <div class="team-card">
                                    <canvas class="header-bg" width="250" height="70" id="header-blur"></canvas>
                                    <div class="avatar">
                                        <img src="<?= Html::encode(Yii::getAlias($member['photo'])) ?>" alt="">
                                    </div>
                                    <div class="team-content">
                                        <h3><?= Html::encode($member['name'])?> <small><?= Html::encode($member['location'])?></small></h3>
                                        <h4><?= Html::encode($member['memberSince']) ?></h4>
                                        <p class="duty"><?= HtmlPurifier::process($member['duty']) ?></p>
                                        <ul class="links-inline links-circle">
                                            <?php
                                            if (isset($member['github'])) {
                                                echo '<li class="github">' . Html::a('<i class="fa fa-github"></i>', 'https://github.com/' . $member['github'], ['title' => $member['name'].' on Github']) . '</li>';
                                            }
                                            if (isset($member['twitter'])) {
                                                echo '<li class="twitter">' . Html::a('<i class="fa fa-twitter"></i>', 'https://twitter.com/' . $member['twitter'], ['title' => $member['name'].' on Twitter']) . '</li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endforeach ?>
                </div>

                <div class="container">
                    <h2>Contributors</h2>

                    <p>
                        There is a huge community of contributors working on the Yii Framework code.
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
                    <p>The following list...</p>

                    <div id="contributors">
                        <?php foreach ($contributors as $contributor) {
                            echo Html::a(Html::img('', ['style="display:inline-block;"', 'class' => 'icon-'. $contributor['login']]), $contributor['html_url'], ['title' => $contributor['login'] . ' on Github']);
                        } ?>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>
