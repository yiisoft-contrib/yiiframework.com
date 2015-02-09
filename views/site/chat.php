<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Live Chat';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-chat">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        We have an IRC channel in the irc.freenode.org server related to yii framework. Simply click on
        <a href="irc://irc.freenode.net/yii">#yii on the Freenode irc network</a> if you have an IRC client.
        Or use the following Web-based IRC client to join the channel and get near-instant help or support
        with the Yii Framework.
    </p>
    <p>
        Please note that the chat is a community effort and that people are not always available or able to help.
        When asking a question, please be patient and stay for a while so that people have a chance to respond
        when they can.
    </p>
    <p style="text-align:center;font-size:1.2em;margin:1em 0;">
        <a href="http://www.yiiframework.com/wiki/369/" target="_blank">Instructions on using MrFisk</a>
    </p>
    <iframe src="http://webchat.freenode.net?channels=yii&uio=OT10cnVlJjExPTE5NSYxMj10cnVl09" width="100%" height="480" style="border:1px solid silver;"></iframe>
</div>
