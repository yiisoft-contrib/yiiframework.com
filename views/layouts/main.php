<?php

use app\controllers\BaseController;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\helpers\Url;
use app\assets\AppAsset;

AppAsset::register($this);

/* @var $this \yii\web\View */
/* @var $content string */

$this->registerLinkTag([
    'rel' => 'search',
    'type' => 'application/opensearchdescription+xml',
    'title' => 'Yii Search',
    'href' => Url::toRoute(['search/opensearch-description']),
]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <link rel="apple-touch-icon" sizes="180x180" href="<?= Yii::getAlias('@web/favico/apple-touch-icon.png') ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= Yii::getAlias('@web/favico/favicon-32x32.png') ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= Yii::getAlias('@web/favico/favicon-16x16.png') ?>">
        <link rel="shortcut icon" href="<?= Yii::getAlias('@web/favico/favicon.ico') ?>">
        <link rel="manifest" href="<?= Yii::getAlias('@web/favico/manifest.json') ?>">
        <link rel="mask-icon" href="<?= Yii::getAlias('@web/favico/safari-pinned-tab.svg') ?>" color="#1e6887">
        <meta name="msapplication-config" content="<?= Yii::getAlias('@web/favico/browserconfig.xml') ?>">
        <meta name="theme-color" content="#1e6887">

        <link href="<?= Url::to(['rss/all'], true) ?>" type="application/rss+xml" rel="alternate" title="Lives News for Yii Framework">

        <?= Html::csrfMetaTags() ?>
        <?php $this->registerJs('yiiBaseUrl = ' . \yii\helpers\Json::htmlEncode(Yii::$app->request->getBaseUrl()), \yii\web\View::POS_HEAD); ?>

        <title><?php
            $title = [];
            if (!empty($this->title)) {
                $title[] = $this->title;
            }
            if ($this->context instanceof BaseController) {
                if ($this->context->headTitle !== null) {
                    $title[] = $this->context->headTitle;
                } elseif ($this->context->sectionTitle !== null) {
                    if (is_array($this->context->sectionTitle)) {
                        foreach(array_reverse($this->context->sectionTitle) as $name => $url) {
                            $title[] = $name;
                        }
                    } else {
                        $title[] = $this->context->sectionTitle;
                    }
                }
            }
            $title[] = 'Yii PHP Framework';

            echo Html::encode(implode(' | ', array_unique($title)));
        ?></title>

        <meta property="og:site_name" content="Yii Framework" />
        <meta property="og:title" content="<?= !empty($this->title) ? Html::encode($this->title) : 'Yii Framework' ?>" />
        <meta property="og:image" content="<?= Url::to(Yii::getAlias('@web/image/facebook_cover.png'), true) ?>" />
        <meta property="og:url" content="<?= Html::encode(Url::to()) ?>" />
        <meta property="og:description" content="" />

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="yiiframework" />
        <meta name="twitter:title" content="<?= !empty($this->title) ? Html::encode($this->title) : 'Yii Framework' ?>" />
        <meta name="twitter:description" content="" />
        <meta name="twitter:image" content="<?= Url::to(Yii::getAlias('@web/image/twitter_cover.png'), true) ?>" />
        <meta name="twitter:image:width" content="120" />
        <meta name="twitter:image:height" content="120" />

        <?php $this->head() ?>

        <?= $this->render('partials/_analytics') ?>
    </head>
    <body data-spy="scroll" data-target="#scrollnav" data-offset="1">
        <?php $this->beginBody() ?>

        <div id="page-wrapper" class="">

            <?= $this->render('partials/_header', ['discourse' => false]); ?>

            <?= $this->context instanceof BaseController && isset($this->context->sectionTitle) ? $this->render('partials/_sectionHeader', ['title' => $this->context->sectionTitle]) : ''; ?>

            <?= \app\widgets\Alert::widget(['options' => ['class' => 'main-alert']]) ?>
            <?= $content ?>

            <?= $this->render('partials/_footer'); ?>

        </div> <!-- close the id="page-wrapper" -->

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
