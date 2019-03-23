<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
?>
<div class="container style_external_links">
    <div class="site-chat content">
        <h1>Live Chat</h1>
        <p>To get near-instant help or support with the Yii Framework you can join either Slack or IRC channel.</p>

        <p>
            Please note that the chats are community effort and that people are not always available or able to help.<br>
            When asking a question, please <strong>be patient and stay for a while</strong> so that people have a chance to respond
            when they can.
        </p>

        <h2>Slack</h2>

        <p>
            We have a Slack channel:
            <a href="https://join.slack.com/t/yii/shared_invite/enQtMzQ4MDExMDcyNTk2LWUzN2RlZmJiMDdiZDIwOWI4M2U3ODYwOTRjZDk5MTY1ZWM3YTY5MTVkNzRiN2RlMWQxODA5N2ZmY2E5NWI2YTM">yii.slack.com</a>.
            There are <a href="https://slack.com/downloads/">handy clients for Desktop and phones</a>.
        </p>

        <h2>IRC</h2>
        <p>
            Our IRC channel is on the <a href="http://freenode.net/">Freenode IRC network</a>. If you already have
            an IRC client installed, simply click on <a href="irc://irc.freenode.net/yii">#yii on the Freenode IRC network</a>.
            Otherwise feel free to use the web-based IRC client below to join the channel and start the chat.
        </p>

        <p>
            We have two bots in the channel: <a href="<?= Yii::getAlias('@web/wiki/369/') ?>" target="_blank">MrFisk</a> and Gillette, which post
            documentation references about the code we talk about.
        </p>

        <iframe title="IRC Client" src="https://kiwiirc.com/nextclient/#irc://irc.freenode.net/#yii" width="100%" height="480" style="border:1px solid silver;"></iframe>
    </div>
</div>
