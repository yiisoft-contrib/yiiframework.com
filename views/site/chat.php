<?php
use yii\helpers\Html;
use yii\web\View;

Yii::setAlias('@kiwi', __DIR__ . '/../../../KiwiIRC');

/* @var $this yii\web\View */
$this->title = 'Live Chat';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container style_external_links">
    <div class="row">
        <div class="site-chat content">
            <p>
                We have an IRC channel on the irc.freenode.org server related to yii framework. Simply click on
                <a href="irc://irc.freenode.net/yii">#yii on the Freenode irc network</a> if you have an IRC client.
                Or use the following Web-based IRC client to join the channel and get near-instant help or support
                with the Yii Framework.
            </p>
            <p>
                Please note that the chat is a community effort and that people are not always available or able to help.
                When asking a question, please be patient and stay for a while so that people have a chance to respond
                when they can.
            </p>

            <p>
                <a href="http://www.yiiframework.com/wiki/369/" target="_blank">Instructions on using MrFisk</a>
            </p>

            <?php /*
            <iframe src="http://webchat.freenode.net?channels=yii&uio=OT10cnVlJjExPTE5NSYxMj10cnVl09" width="100%" height="480" style="border:1px solid silver;"></iframe>

            <iframe src="https://kiwiirc.com/client/irc.freenode.net/?theme=mini#yii" style="border:0; width:100%; height:480px;"></iframe>
            */ ?>


            <?php

            if (preg_match('~<body>(.*)</body>~si', file_get_contents(Yii::getAlias('@kiwi/client/index.html')), $matches)) {
                $this->registerMetaTag(['name' => 'referrer', 'content' => 'origin-when-crossorigin']);

                $kiwiHtml = $matches[1];
                // remove jquery, it is already here
                $kiwiHtml = preg_replace('~<script src=".+?jquery.+?js"></script>~', '', $kiwiHtml);
                // set base path for the kiwi server
                $kiwiHtml = preg_replace('~var base_path = \'/kiwi\'~', 'var base_path = \'http://localhost:7778/kiwi\'', $kiwiHtml);

                Yii::$app->assetManager->bundles[app\assets\AppAsset::class]['jsOptions']['position'] = View::POS_BEGIN;

                echo $kiwiHtml;

            } else {
                throw new \yii\base\Exception('Unable to extract Kiwi template data.');
            }

            ?>
        </div>
    </div>
</div>
