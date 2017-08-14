<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Live Chat';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container style_external_links">
    <div class="row">
        <div class="site-chat content">
            <p>To get near-instant help or support with the Yii Framework you can join either Slack or IRC channel.</p>

            <p>
                Please note that the chats are community effort and that people are not always available or able to help.
                When asking a question, please be patient and stay for a while so that people have a chance to respond
                when they can.
            </p>

            <h2>Slack</h2>

            <p>We have a Slack channel: <a href="https://join.slack.com/t/yii/shared_invite/MjIxMjMxMTk5MTU1LTE1MDE3MDAwMzMtM2VkMTMyMjY1Ng">yii.slack.com</a>.</p>
            
            <p>There are <a href="https://slack.com/downloads/">handy clients for Desktop and phones</a>.</p>

            <h2>IRC</h2>
            <p>
                Our IRC channel is on the irc.freenode.org server. Simply click on
                <a href="irc://irc.freenode.net/yii">#yii on the Freenode IRC network</a> if you have an IRC client.
                Or use the following Web-based IRC client to join the channel.
            </p>


            <p>
                <a href="/wiki/369/" target="_blank">Instructions on using MrFisk</a>
            </p>

            <iframe src="http://webchat.freenode.net?channels=yii&uio=OT10cnVlJjExPTE5NSYxMj10cnVl09" width="100%" height="480" style="border:1px solid silver;"></iframe>
        </div>
    </div>
</div>
