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
  <div class="top-header hidden-xs hidden-sm hidden-md">
      <div class="container">
          <div class="pull-left">
              <div class="header-item"><?= Html::a('The Definitive Guide', ['guide/entry']) ?></div>
              <div class="header-item"><?= Html::a('Class Reference', ['api/index', 'version' => reset(Yii::$app->params['api.versions'])]) ?></div>
          </div>
          <div class="pull-right">
              <?php if (Yii::$app->user->isGuest): ?>
                  <div class="header-item"><?= Html::a('<i class="fa fa-sign-in"></i>Login</a>', ['/site/login']) ?></div>
                  <div class="header-item"><?= Html::a('<i class="fa fa-user"></i>Sign up</a>', ['/site/signup']) ?></div>
              <?php else: ?>
                  <div class="header-item">Welcome, <?= Yii::$app->user->identity->username ?>!</div>
                  <div class="header-item"><?= Html::a('<i class="fa fa-sign-out"></i>Logout</a>', ['/site/logout'], ['data-method' => 'post']) ?></div>
              <?php endif; ?>
          </div>
      </div>
  </div>
  <div class="clearfix"></div>
	<header class="navbar navbar-inverse navbar-static">
    	<div class="container">
            <div id="main-nav-head" class="navbar-header">
                <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                    <span>
                        <img src="data:image/png;base64,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" alt="Yii Logo">
                        <span class="nudge-left">yii<span class="framework-gray">framework</span></span>
                    </span>
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
                            ['label' => 'Learn', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i> Getting started', 'url' => ['site/tour']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> The Definitive Guide', 'url' => ['guide/entry']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> API Documentation', 'url' => ['api/index', 'version' => reset(Yii::$app->params['api.versions'])]],
                                //['label' => '<i class="fa fa-angle-double-right"></i>Tutorials<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/tutorials'],
                                //['label' => '<i class="fa fa-angle-double-right"></i>Answers<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/answers'],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Books', 'url' => ['site/books']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Resources', 'url' => ['site/resources']],
                            ]],
                            ['label' => 'Develop', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i> Install Yii', 'url' => ['site/download']],
                                //['label' => '<i class="fa fa-angle-double-right"></i>Extensions<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/extensions'],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Report an Issue', 'url' => ['site/report-issue']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Report a Security Issue', 'url' => ['site/security']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Contribute to Yii', 'url' => ['/site/contribute']],
                                //['label' => '<i class="fa fa-angle-double-right"></i>Jobs<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/jobs'],
                            ]],
                            ['label' => 'Discuss', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i> Forum', 'url' => '/forum'],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Live Chat', 'url' => ['site/chat']],
                            ]],
                            ['label' => 'About', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i> What is Yii?', 'url' => ['site/about']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> News', 'url' => ['site/news']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> License', 'url' => ['site/license']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Team', 'url' => ['site/team']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Official logo', 'url' => ['site/logo']],
                            ]],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest, 'options' => ['class' => 'hidden-lg']],
                            ['label' => 'Signup', 'url' => ['site/signup'], 'visible' => Yii::$app->user->isGuest, 'options' => ['class' => 'hidden-lg']],
                            ['label' => 'Logout', 'url' => ['site/logout'], 'visible' => !Yii::$app->user->isGuest, 'linkOptions' => ['data-method' => 'post'], 'options' => ['class' => 'hidden-lg']],
                        ],
                    ]);
?>
                <!-- Search dropdown - only visible on larger screens -->
                <ul class="nav navbar-nav visible-lg">
                    <li class="dropdown search-form-toggle">
                        <a href="#" class="dropdown-toggle" title="Search" data-toggle="dropdown"><i class="fa fa-search"></i></a>
                        <ul class="dropdown-menu navbar-search-form">
                          	<li>
                                <ul class="nav navbar-nav">
                                    <li>
                                        <?= $this->render('_searchForm'); ?>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right hidden-lg">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><b>Search</b> <span class="caret"></span></a>
                        <ul id="search-dp" class="dropdown-menu">
                            <li>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $this->render('_searchForm'); ?>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
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
            	<div class="col-md-4">
                	<div class="footer-header"><i class="fa fa-flag fa-1g"></i>Contact</div>
                    <p class="contact-text">Use <?= Html::a('contact form', ['site/contact']) ?> if you have consulting requests or any other proposals.</p>
                    <ul class="brands brands-inline brands-sm brands-transition brands-circle">
                        <li><a href="https://github.com/yiisoft/yii2" class="brands-github"><i class="fa fa-github"></i></a></li>
                    	<li><a href="https://twitter.com/yiiframework" class="brands-twitter"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="https://www.facebook.com/groups/yiitalk/" class="brands-facebook"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://www.linkedin.com/groups/yii-framework-1483367" class="brands-linkedin"><i class="fa fa-linkedin"></i></a></li>
                    </ul>
                </div>

                <div class="col-md-4 fa-1g">
                	<div class="footer-header"><i class="fa fa-heart-o"></i>Our supporters</div>
                    <div class="row" id="latest-work-footer">
						<div class="overlay-wrapper col-sm-6">
                            <a href="https://www.jetbrains.com/"><img src="<?= Yii::getAlias('@web/image/logo_jetbrains.png') ?>" class="img-responsive" alt="JetBrains"></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 hidden-xs hidden-sm">
                	<div class="footer-header"><i class="fa fa-envelope"></i>Newsletter</div>
                    <p class="contact-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nec lorem quis est ultrices volutpat.</p>
                    <form>
                    	<fieldset>
                        	<div class="form-group nospace">
                            	<div class="input-group">
                                    <input type="email" class="form-control" id="email" placeholder="Put your email" required>
                                    <span class="input-group-btn"><button class="btn btn-primary" type="button">Submit</button></span>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; 2015 Yii Software LLC | <?= Html::a('Terms of service', ['/site/tos']) ?></p>
                </div>
            </div>
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
