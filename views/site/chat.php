<?php
use yii\helpers\Url;

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
            <a href="<?= Url::to(['go/slack']) ?>">yii.slack.com</a>.
            There are <a href="https://slack.com/downloads/">handy clients for Desktop and phones</a>.
        </p>

        <h2>IRC</h2>
        <p>
            Our IRC channel is on the <a href="https://libera.chat/">Libera Chat IRC network</a>. If you already have
            an IRC client installed, simply click on <a href="ircs://irc.libera.chat:6697/yii">#yii on the Libera Chat IRC network</a>.
            Otherwise feel free to use the web-based IRC client below to join the channel and start the chat.
        </p>
        <iframe title="IRC Client" src="https://kiwiirc.com/nextclient/#ircs://irc.libera.chat:6697/yii" width="100%" height="480" style="border:1px solid silver;"></iframe>
    </div>
</div>
