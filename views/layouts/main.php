<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\widgets\Menu;

/* @var $this \yii\web\View */
/* @var $content string */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <link rel="shortcut icon" href="<?= Yii::getAlias('@web/favicon.ico') ?>" />
    <link rel="apple-touch-icon" sizes="57x57" href="<?= Yii::getAlias('@web/apple-icon-57x57.png') ?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= Yii::getAlias('@web/apple-icon-60x60.png') ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= Yii::getAlias('@web/apple-icon-72x72.png') ?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= Yii::getAlias('@web/apple-icon-76x76.png') ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= Yii::getAlias('@web/apple-icon-114x114.png') ?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= Yii::getAlias('@web/apple-icon-120x120.png') ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= Yii::getAlias('@web/apple-icon-144x144.png') ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= Yii::getAlias('@web/apple-icon-152x152.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= Yii::getAlias('@web/apple-icon-180x180.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?= Yii::getAlias('@web/android-icon-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= Yii::getAlias('@web/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= Yii::getAlias('@web/favicon-96x96.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= Yii::getAlias('@web/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= Yii::getAlias('@web/manifest.json') ?>">
    <meta name="msapplication-TileColor" content="#4394F0">
    <meta name="msapplication-TileImage" content="<?= Yii::getAlias('@web/ms-icon-144x144.png') ?>">
    <meta name="theme-color" content="#4394F0">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <?= Html::csrfMetaTags() ?>
    <?= Html::cssFile(YII_DEBUG ? '@web/css/all.css' : '@web/css/all.min.css?v=' . filemtime(Yii::getAlias('@webroot/css/all.min.css'))) ?>
    <?php $this->registerJs('yiiBaseUrl = ' . \yii\helpers\Json::htmlEncode(Yii::$app->request->getBaseUrl()), \yii\web\View::POS_HEAD); ?>

    <title><?php if (!empty($this->title)): ?><?= Html::encode($this->title) ?> - <?php endif?>Yii PHP Framework</title>
    <?php $this->head() ?>
</head>
<body data-spy="scroll" data-target="#scrollnav" data-offset="1">
<?php $this->beginBody() ?>

    <div id="page-wrapper" class="">

	<!-- ==========================
    	HEADER - START
    =========================== -->
	<header class="navbar navbar-inverse navbar-static" id="top">
    	<div class="container">
            <div id="main-nav-head" class="navbar-header">
                <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                    Yii Framework
                </a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-inverse fa-bars"></i></button>
            </div>
            <div class="navbar-collapse collapse navbar-right">
                <?php

                    // main navigation
                    echo Nav::widget([
                        'id' => 'main-nav',
                        'encodeLabels' => false,
                        'options' => ['class' => 'nav navbar-nav navbar-main-menu'],
                        'activateItems' => false,
                        'dropDownCaret' => '<span class="caret"></span>',
                        'items' => [
                            ['label' => 'Guide', 'url' => ['guide/entry'], 'options' => ['title' => 'The Definitive Guide to Yii']],
                            ['label' => 'API', 'url' => ['api/index', 'version' => reset(Yii::$app->params['api.versions'])], 'options' => ['title' => 'API Documentation']],
                            ['label' => 'Wiki', 'url' => ['site/wiki'], 'options' => ['title' => 'Community Wiki']],
                            ['label' => 'Extensions', 'options' => ['title' => 'Not Yet']],
                            ['label' => 'More&hellip;', 'options' => ['title' => 'Yii Quick Links'], 'items' => [
                                ['label' => 'Learn', 'options' => ['class' => 'separator']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> The Yii Tour', 'url' => ['site/tour']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Books', 'url' => ['site/books']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Resources', 'url' => ['site/resources']],
                                ['label' => 'Develop', 'options' => ['class' => 'separator']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Install Yii', 'url' => ['site/download']],
                                //['label' => '<i class="fa fa-angle-double-right"></i>Extensions<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/extensions'],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Report an Issue', 'url' => ['site/report-issue']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Report a Security Issue', 'url' => ['site/security']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Contribute to Yii', 'url' => ['/site/contribute']],
                                ['label' => 'Discuss', 'options' => ['class' => 'separator']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Live Chat', 'url' => ['site/chat']],
                                ['label' => 'About', 'options' => ['class' => 'separator']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> What is Yii?', 'url' => ['site/about']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> News', 'url' => ['site/news']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> License', 'url' => ['site/license']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Team', 'url' => ['site/team']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Official logo', 'url' => ['site/logo']],
                            ]],
                            //['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest, 'options' => ['class' => 'hidden-lg']],
                            //['label' => 'Signup', 'url' => ['site/signup'], 'visible' => Yii::$app->user->isGuest, 'options' => ['class' => 'hidden-lg']],
                            //['label' => 'Logout', 'url' => ['site/logout'], 'visible' => !Yii::$app->user->isGuest, 'linkOptions' => ['data-method' => 'post'], 'options' => ['class' => 'hidden-lg']],
                        ],
                    ]);
?>
                <div class="nav navbar-nav navbar-right">
                    <?= $this->render('_searchForm'); ?>
                </div>
        </div>
    </header>
    <!-- ==========================
    	HEADER - END
    =========================== -->

    <?= $content ?>

    <!-- ==========================
    	FOOTER - START
    =========================== -->
    <footer>
    	<div class="container">
        	<div class="row">
        </div>
    </footer>
    </div>
   	<!-- ==========================
    	JS
    =========================== -->
    <?= Html::jsFile(YII_DEBUG ? '@web/js/all.js' : '@web/js/all.min.js?v=' . filemtime(Yii::getAlias('@webroot/js/all.min.js'))) ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
