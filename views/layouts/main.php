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

    <link href='https://fonts.googleapis.com/css?family=Josefin+Sans:600|Open+Sans' rel='stylesheet' type='text/css'>

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
                  <div class="header-item"><?= Html::a('<i class="fa fa-sign-in"></i>Login', ['/site/login']) ?></div>
                  <div class="header-item"><?= Html::a('<i class="fa fa-user"></i>Sign up', ['/site/signup']) ?></div>
              <?php else: ?>
                  <div class="header-item">Welcome, <?= Yii::$app->user->identity->username ?>!</div>
                  <div class="header-item"><?= Html::a('<i class="fa fa-sign-out"></i>Logout', ['/site/logout'], ['data-method' => 'post']) ?></div>
              <?php endif; ?>
          </div>
      </div>
  </div>
	<header class="navbar navbar-inverse navbar-static">
    	<div class="container">
            <div id="main-nav-head" class="navbar-header">
                <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                    <img src="data:image/png;base64,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" alt="Yii Logo">
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
                                ['label' => '<i class="fa fa-angle-double-right"></i> The Yii Tour', 'url' => ['site/tour']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> The Definitive Guide', 'url' => ['guide/entry']],
                                ['label' => '<i class="fa fa-angle-double-right"></i> API Documentation', 'url' => ['api/index', 'version' => reset(Yii::$app->params['api.versions'])]],
                                ['label' => '<i class="fa fa-angle-double-right"></i> Wiki', 'url' => ['site/wiki']],
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
