<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Resources';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-resources">
    <div class="header container">
        <div class="row">
            <div class="col-xs-12">
                <h1>Resources</h1>
                <h2>Very helpful. Check these out.</h2>
            </div>
        </div>
        <img class="background" src="<?= Yii::getAlias('@web/image/resources/header.svg')?>" alt="">
    </div>
    <div class="container style_external_links">
        <div class="row">
            <div class="col-xs-12">
                <div class="content">
                    <p>There are various resources that aren't part of official Yii website but are very helpful. Check these out.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-3 group">
                <h3>News</h3>

                <div class="image">
                    <a href="https://yiifeed.com/" aria-label="YiiFeed">
                    <img src="<?= Yii::getAlias('@web/image/resources/yiifeed.png') ?>" alt="YiiFeed">
                    </a>
                </div>

                <h4><a href="https://yiifeed.com/">YiiFeed</a></h4>

                <p>is a community-driven news source for both official Yii announcements and
                    unofficial articles, blogposts and tutorials. Anyone can suggest news. RSS provided.</p>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-3 group">
                <h3>Showcase</h3>

                <div class="image">
                    <a href="https://yiipowered.com/en" aria-label="YiiPowered">
                    <img src="<?= Yii::getAlias('@web/image/resources/yiipowered.png') ?>" alt="YiiPowered">
                    </a>
                </div>

                <h4><a href="https://yiipowered.com/en">YiiPowered</a></h4>

                <p>Community-powered showcase of projects and websites built with Yii including OpenSource projects.</p>

                <p>Projects could be added by anyone and are published shortly after moderation.</p>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-3 group">
                <h3>Videos</h3>

                <div class="image">
                    <img src="<?= Yii::getAlias('@web/image/resources/videos.png') ?>" alt="Videos">
                </div>

                <p>There are many videos available. <a href="https://www.youtube.com/results?search_query=yii">Check YouTube for "yii"</a>.</p>
                <p>Below are links to two big video series community likes most.</p>

                <ul>
                    <li><a href="https://www.youtube.com/playlist?list=PLMyGpiUTm106xkNQh9WeMsa-LXjanaLUm">Beginning Yii 2.0 by Tom King</a></li>
                    <li><a href="https://www.youtube.com/playlist?list=PLRd0zhQj3CBmusDbBzFgg3H20VxLx2mkF">Yii2 Lessons, DoingITeasyChannel</a></li>
                    <li><a href="https://www.youtube.com/watch?v=aq0A2o6nGuA&amp;list=PLpNYlUeSK_rn_3mWq_vPt_jKz6cp7a6sZ">Yii 2.0 by Luke Briner</a></li>
                </ul>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-3 group">
                <h3>Yii 1.1</h3>

                <div class="image">
                    <a href="<?= Url::to(['guide/blog-entry']) ?>" aria-label="Yii 1.1 Blog tutorial">
                    <img src="<?= Yii::getAlias('@web/image/resources/yii11.png') ?>" alt="Yii 1.1">
                    </a>
                </div>

                <h4><?= Html::a('The Yii 1.1 Blog tutorial', ['guide/blog-entry']) ?></h4>

                <p>If you need to learn good old Yii 1.1 this is a must read official tutorial.</p>
            </div>
        </div>
    </div>
</div>
