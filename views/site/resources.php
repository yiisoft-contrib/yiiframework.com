<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Resources';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="row">
        <div class="content">
            <p>There are various resources that aren't part of official Yii website but are very helpful. Check these out.</p>

            <h2>News</h2>

            <p><a href="http://yiifeed.com/">YiiFeed</a> is a community-driven news source for both official Yii announcements and
            unofficial articles, blogposts and tutorials. Anyone can suggest news. RSS provided.</p>

            <h2>Extensions</h2>

            <p>Until official extensions repository is ready you can use <a href="https://yiigist.com/">YiiGist</a> which is community driven project based on packagist.</p>

            <h2>Videos</h2>

            <p>There are many videos available. Check YouTube for "yii". Below are links to two big video series community likes most.</p>

            <ul>
                <li><a href="https://www.youtube.com/playlist?list=PLMyGpiUTm106xkNQh9WeMsa-LXjanaLUm">Beginning Yii 2.0 by Tom King</a></li>
                <li><a href="https://www.youtube.com/playlist?list=PLRd0zhQj3CBmusDbBzFgg3H20VxLx2mkF">Yii2 Lessons, DoingITeasyChannel</a></li>
            </ul>

            <h2>Yii 1.1</h2>

            <ul>
                <li><?= Html::a('The Yii 1.1 Blog tutorial', ['guide/blog-entry']) ?></li>
            </ul>
        </div>
    </div>
</div>
