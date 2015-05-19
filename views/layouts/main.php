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

	<link rel="shortcut icon" href="<?= Yii::getAlias('@web/favicon.ico') ?>" />
	<!-- TODO
	link rel="apple-touch-icon" href="icons/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="icons/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="icons/apple-touch-icon-114x114.png"-->

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <?= Html::csrfMetaTags() ?>
    <?= Html::cssFile(YII_DEBUG ? '@web/css/all.css' : '@web/css/all.min.css?v=' . filemtime(Yii::getAlias('@webroot/css/all.min.css'))) ?>

    <title><?php if (!empty($this->title)): ?><?= Html::encode($this->title) ?> - <?php endif?>Yii PHP Framework</title>
    <?php $this->head() ?>
</head>
<body class="color-yii">
<?php $this->beginBody() ?>

    <div id="page-wrapper" class="">

	<!-- ==========================
    	HEADER - START
    =========================== -->
    <div class="top-header hidden-xs hidden-sm">
        <div class="container">
            <div class="pull-left">
                <div class="header-item"><?= Html::a('The Definitive Guide', ['guide/index', 'version' => '2.0', 'language' => 'en']) ?></div>
                <div class="header-item"><?= Html::a('Class Reference', ['api/index', 'version' => '2.0']) ?></div>
            </div>
            <div class="pull-right">
                <?php if (Yii::$app->user->isGuest): ?>
                    <div class="header-item"><?= Html::a('<i class="fa fa-sign-in"></i>Login</a>', ['/site/login']) ?></div>
                    <div class="header-item"><?= Html::a('<i class="fa fa-user"></i>Sign up</a>', ['/site/signup']) ?></div>
                <?php else: ?>
                    <div class="header-item">Welcome, <?= Yii::$app->user->identity->username ?>!</div>
                    <div class="header-item"><?= Html::a('<i class="fa fa-sign-out"></i>Logout</a>', ['/site/logout'], ['data-method' => 'post']) ?></div>
                <?php endif; ?>
                <ul class="brands brands-inline brands-tn brands-circle main">
                    <li><a href="https://github.com/yiisoft/yii2"><i class="fa fa-github"></i></a></li>
                    <li><a href="https://twitter.com/yiiframework"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="https://www.facebook.com/groups/yiitalk/"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="https://www.linkedin.com/groups/yii-framework-1483367"><i class="fa fa-linkedin"></i></a></li>
                    <!--li><a href="#"><i class="fa fa-google-plus"></i></a></li-->
                </ul>
                <!-- TODO this link is not shown on mobile so it has to find a better place
                <div class="header-item"><a href="signin.html" class="pull-right"><i class="fa fa-user"></i>Sign in</a></div>
                -->
            </div>
        </div>
    </div>

	<header class="navbar navbar-default navbar-static-top">
    	<div class="container">
            <div class="navbar-header">
                <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                    <img src="<?= Yii::getAlias('@web/image/logo.png') ?>" class="logo">
                    <?php /* <object type="image/svg+xml" data="<?= Yii::getAlias('@web/logo.svg') ?>" class="logo"></object><span class="hidden-sm"> Yii Framework</span>*/ ?>
                </a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-bars"></i></button>
            </div>
            <div class="navbar-collapse collapse navbar-right">

                <ul class="nav navbar-nav"><li>
                        <?= Html::beginForm(['/search/global'], 'get', ['id' => 'search-form', 'class' => 'navbar-form']) ?>
                        <div class="form-group nospace">
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="q" placeholder="Search" value="<?= property_exists($this->context, 'searchQuery') ? Html::encode($this->context->searchQuery) : '' ?>">
                                <span class="input-group-btn"><?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => "btn btn-primary"]) ?></span>
                            </div>
                        </div>
                        <?= Html::endForm() ?>
                    </li></ul>

            <?php

//                    // search form
//                    $form = ActiveForm::begin([
//        	            'id' => 'search-form',
//        	            'options' => [
//        		            'class' => 'navbar-form navbar-left',
//        		            'role' => 'search',
//        	            ],
//                    ]);
//                        echo '<div class="form-group">';
//                        echo Html::input('text', 'q', '', ['class' => 'form-control', 'placeholder' => 'Search...', 'aria-label' => 'Search terms']) . ' ';
//                        echo Html::button('Search!', ['type' => 'submit', 'class' => 'btn btn-default']);
//        			    echo '</div>';
//                    ActiveForm::end();

                    // main navigation
                    echo Nav::widget([
                        'encodeLabels' => false,
                        'options' => ['class' => 'nav navbar-nav navbar-main-menu'],
                        'activateItems' => false,
                        'dropDownCaret' => '<i class="fa fa-chevron-down"></i>',
                        'items' => [
                            ['label' => 'About', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i>What is Yii?', 'url' => ['site/about']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>News', 'url' => ['site/news']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>License', 'url' => ['site/license']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Team', 'url' => ['site/team']],
                            ]],
                            ['label' => 'Learn', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i>Getting started', 'url' => ['guide/view', 'section' => 'start-installation', 'language' => 'en', 'version' => key(Yii::$app->params['guide.versions'])]],
                                ['label' => '<i class="fa fa-angle-double-right"></i>The Definitive Guide', 'url' => ['guide/entry']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>API Documentation', 'url' => ['api/index', 'version' => reset(Yii::$app->params['api.versions'])]],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Tutorials<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/tutorials'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Answers<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/answers'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Books', 'url' => ['site/books']],
                            ]],
                            ['label' => 'Develop', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i>Install Yii', 'url' => ['site/download']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Extensions<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/extensions'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Report an Issue', 'url' => ['site/report-issue']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Report a Security Issue', 'url' => ['site/security']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Contribute to Yii', 'url' => ['/site/contribute']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Jobs<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/jobs'],
                            ]],
                            ['label' => 'Discuss', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i>Forum', 'url' => '/forum'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Live Chat', 'url' => ['site/chat']],
                            ]],
                            ['label' => 'Camp', 'url' => 'https://yiicamp.com'],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest, 'options' => ['class' => 'hidden-lg hidden-md']],
                            ['label' => 'Signup', 'url' => ['site/signup'], 'visible' => Yii::$app->user->isGuest, 'options' => ['class' => 'hidden-lg hidden-md']],
                            ['label' => 'Logout', 'url' => ['site/logout'], 'visible' => !Yii::$app->user->isGuest, 'linkOptions' => ['data-method' => 'post'], 'options' => ['class' => 'hidden-lg hidden-md']],
                        ],
                    ]);
?>
<!--                <ul class="nav navbar-nav">
                    <li class="dropdown search-form-toggle">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search"></i></a>
                        <ul class="dropdown-menu navbar-search-form">
                        	<li>
                            	<form>
                                    <fieldset>
                                        <div class="form-group nospace">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="search" placeholder="Search" required>
                                                <span class="input-group-btn"><button class="btn btn-primary" type="button"><i class="fa fa-search"></i></button></span>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>-->
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
                	<h3><i class="fa fa-flag"></i>Contact</h3>
                    <p class="contact-text">Use <?= Html::a('contact form', ['site/contact']) ?> if you have consulting requests or any other proposals.</p>
<!--                    <ul class="list-unstyled contact-address">-->
<!--                        <li>Yii Software LLC</li>-->
<!--                    </ul>-->
                    <ul class="brands brands-inline brands-sm brands-transition brands-circle">
                        <li><a href="https://github.com/yiisoft/yii2" class="brands-github"><i class="fa fa-github"></i></a></li>
                    	<li><a href="https://twitter.com/yiiframework" class="brands-twitter"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="https://www.facebook.com/groups/yiitalk/" class="brands-facebook"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://www.linkedin.com/groups/yii-framework-1483367" class="brands-linkedin"><i class="fa fa-linkedin"></i></a></li>
                    	<!--li><a href="#" class="brands-facebook"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#" class="brands-twitter"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#" class="brands-google-plus"><i class="fa fa-google-plus"></i></a></li>
                        <li><a href="#" class="brands-linkedin"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="#" class="brands-youtube"><i class="fa fa-youtube"></i></a></li>
                        <li><a href="#" class="brands-skype"><i class="fa fa-skype"></i></a></li-->
                    </ul>
                </div>

                <div class="col-md-4 hidden-xs hidden-sm">
                	<h3><i class="fa fa-heart-o"></i>Our supporters</h3>
                    <div class="row" id="latest-work-footer">
						<div class="overlay-wrapper col-sm-6">
                            <a href="https://www.jetbrains.com/"><img src="image/logo_jetbrains.png" class="img-responsive" alt="JetBrains"></a>
                        </div>
                        <?php /*
                        <div class="overlay-wrapper col-sm-6">
                            <img src="image/image_02.jpg" class="img-responsive" alt="">
                            <span class="overlay">
                                <a href="portfolio-post1.html"><i class="fa fa-plus"></i></a>
                            </span>
                        </div>
                        <div class="overlay-wrapper col-sm-6">
                            <img src="image/image_05.jpg" class="img-responsive" alt="">
                            <span class="overlay">
                                <a href="portfolio-post1.html"><i class="fa fa-plus"></i></a>
                            </span>
                        </div>
                        <div class="overlay-wrapper col-sm-6">
                            <img src="image/image_06.jpg" class="img-responsive" alt="">
                            <span class="overlay">
                                <a href="portfolio-post1.html"><i class="fa fa-plus"></i></a>
                            </span>
                        </div>
                        */ ?>
                    </div>
                </div>

                <div class="col-md-4 hidden-xs hidden-sm">
                	<h3><i class="fa fa-envelope"></i>Newsletter</h3>
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
                    <p>&copy; 2015 Yii Software LLC</p>
                </div>
                <ul class="nav navbar-nav navbar-right hidden-xs hidden-sm">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="features.html">Features</a></li>
                    <li><a href="blog2.html">Blog</a></li>
                    <li><a href="portfolio1.html">Portfolio</a></li>
                    <li><a href="contact1.html">Contact</a></li>
                </ul>
            </div>
        </div>
    </footer>
    <!-- ==========================
    	FOOTER - END
    =========================== -->

    </div>

   	<!-- ==========================
    	JS
    =========================== -->
    <?= Html::jsFile(YII_DEBUG ? '@web/js/all.js' : '@web/js/all.min.js?v=' . filemtime(Yii::getAlias('@webroot/js/all.min.js'))) ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
