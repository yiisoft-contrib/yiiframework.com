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

    <!-- TODO host these ourselfs -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <?= Html::csrfMetaTags() ?>
    <?= Html::cssFile(YII_DEBUG ? '@web/css/all.css' : '@web/css/all.min.css?v=' . filemtime(Yii::getAlias('@webroot/css/all.min.css'))) ?>

    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="color-light-blue">
<?php $this->beginBody() ?>

	<!-- ==========================
    	FACEBOOK - SHARE BUTTON
    =========================== -->
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    <!-- ==========================
    COLOR SWITCHER - START
    =========================== - - >
    <div id="color-switcher">
        <div id="toggle-switcher"><i class="fa fa-gear"></i></div>
        <span>Color Scheme:</span>
        <ul class="list-unstyled list-inline">
            <li id="blue" data-toggle="tooltip" data-placement="top" title="Blue"></li>
            <li id="green" data-toggle="tooltip" data-placement="top" title="Green"></li>
            <li id="orange" data-toggle="tooltip" data-placement="top" title="Orange"></li>
            <li id="red" data-toggle="tooltip" data-placement="top" title="Red"></li>
            <li id="purple" data-toggle="tooltip" data-placement="top" title="Purple"></li>
            <li id="light-blue" data-toggle="tooltip" data-placement="top" title="Light Blue"></li>
            <li id="yellow" data-toggle="tooltip" data-placement="top" title="Yellow"></li>
            <li id="pink" data-toggle="tooltip" data-placement="top" title="Pink"></li>
            <li id="light-green" data-toggle="tooltip" data-placement="top" title="Light Green"></li>
            <li id="black" data-toggle="tooltip" data-placement="top" title="Black"></li>
        </ul>

        <button id="page-boxed-toggle" class="btn btn-primary">Boxed Page</button>
    </div>
    <!-- ==========================
        COLOR SWITCHER - END
    =========================== -->

    <div id="page-wrapper" class="">

	<!-- ==========================
    	HEADER - START
    =========================== -->
    <div class="top-header hidden-xs hidden-sm">
    	<div class="container">
        	<?php /*<div class="pull-left">
            	<div class="header-item"><i class="fa fa-envelope"></i> info@pixlized.cz</div>
                <div class="header-item"><i class="fa fa-phone"></i> +420 123 456 789</div>
            </div> */ ?>
            <div class="pull-right">
            	<ul class="brands brands-inline brands-tn brands-circle main">
                    <li><a href="https://github.com/yiisoft/yii2"><i class="fa fa-github"></i></a></li>
                	<li><a href="https://twitter.com/yiiframework"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="https://www.facebook.com/groups/yiitalk/"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="https://www.linkedin.com/groups/yii-framework-1483367"><i class="fa fa-linkedin"></i></a></li>
                    <!--li><a href="#"><i class="fa fa-google-plus"></i></a></li-->
                </ul>
                <div class="header-item"><a href="signin.html" class="pull-right"><i class="fa fa-user"></i>Sign in</a></div>
            </div>
        </div>
    </div>

	<header class="navbar yamm navbar-default navbar-static-top">
    	<div class="container">
            <div class="navbar-header">
                <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                    <img src="<?= Yii::getAlias('@web/image/yii.png') ?>" class="logo">
                    <?php /* <object type="image/svg+xml" data="<?= Yii::getAlias('@web/logo.svg') ?>" class="logo"></object><span class="hidden-sm"> Yii Framework</span>*/ ?>
                </a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-bars"></i></button>
            </div>
            <div class="navbar-collapse collapse navbar-right">
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
                        'items' => [
                            ['label' => '<span class="glyphicon glyphicon-book" aria-hidden="true"></span> About <i class="fa fa-chevron-down"></i>', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i>What is Yii?', 'url' => '#'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>News', 'url' => '#'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>License', 'url' => '#'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Team', 'url' => '#'],
                            ]],
                            ['label' => '<span class="glyphicon glyphicon-book" aria-hidden="true"></span> Learn <i class="fa fa-chevron-down"></i>', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i>The Definitive Guide', 'url' => ['guide/index', 'version' => '2.0', 'language' => 'en']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Class Reference', 'url' => ['api/index', 'version' => '2.0']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Tutorials<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/tutorials'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Answers<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/answers'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Books', 'url' => ['site/books']],
                            ]],
                            ['label' => '<span class="glyphicon glyphicon-star" aria-hidden="true"></span> Develop', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i>Install Yii', 'url' => ['guide/view', 'version' => '2.0', 'language' => 'en', 'section' => 'start-installation']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Extensions<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/extensions'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Report an Issue', 'url' => 'https://github.com/yiisoft/yii2/issues/new'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Report a Security Issue', 'url' => 'https://github.com/yiisoft/yii2/issues/new'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Contribute to Yii', 'url' => ['/site/contribute']],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Jobs<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/jobs'],
                            ]],
                            <<<HTML
<li class="dropdown yamm-fw hidden-xs hidden-sm">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Wide Menu <i class="fa fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                        	<li>
                            	<div class="yamm-content">
                                    <div class="row">
                                        <div class="col-sm-6">
                                        	<h3>Our Latest Project</h3>
                                            <div class="row">
                                            	<div class="col-sm-6">
                                            		<img src="image/image_02.jpg" class="img-responsive" alt="">
                                            		<a href="portfolio-post1.html" class="btn btn-primary">Show Me</a>
                                                </div>
                                                <div class="col-sm-6">
                                                	<p><b>Any idea how to use this feature? :)</b> amet, consectetur adipiscing elit. Duis nec lorem quis est ultrices volutpat. Donec id urna posuere nisl tincidunt laoreet. Aliquam erat volutpat. Duis eu sapien auctor, bibendum ante ut, volutpat purus. </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        	<h3>Features Pages</h3>
                                        	<ul class="nav vertical-nav">
                                            	<li><a href="about.html"><i class="fa fa-angle-double-right"></i>About</a></li>
                                                <li><a href="pricing1.html"><i class="fa fa-angle-double-right"></i>Pricing 1</a></li>
                                                <li><a href="pricing2.html"><i class="fa fa-angle-double-right"></i>Pricing 2</a></li>
                                                <li><a href="pricingtable.html"><i class="fa fa-angle-double-right"></i>Pricing Table</a></li>
                                                <li><a href="services1.html"><i class="fa fa-angle-double-right"></i>Services 1</a></li>
                                                <li><a href="services2.html"><i class="fa fa-angle-double-right"></i>Services 2</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-3">
                                            <h3>About Us</h3>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nec lorem quis est ultrices volutpat. Donec id urna posuere nisl tincidunt laoreet. Aliquam erat volutpat. Duis eu sapien auctor, bibendum ante ut, volutpat purus.</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
HTML
                            ,

                            ['label' => '<span class="glyphicon glyphicon-comment" aria-hidden="true"></span> Discuss', 'items' => [
                                ['label' => '<i class="fa fa-angle-double-right"></i>Forum', 'url' => '/forum'],
                                ['label' => '<i class="fa fa-angle-double-right"></i>Live Chat', 'url' => ['site/chat']],
//                                ['label' => 'GitHub', 'url' => 'https://github.com/yiisoft/yii2'],
//                                ['label' => 'Facebook', 'url' => 'https://www.facebook.com/groups/yiitalk/'],
//                                ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/groups/yii-framework-1483367'],
//                                ['label' => 'Twitter', 'url' => 'https://twitter.com/yiiframework'],
                            ]],
                            ['label' => '<span class="glyphicon glyphicon-tent" aria-hidden="true"></span> Camp', 'url' => 'https://yiicamp.com']
                        ],
                    ]);
?>
                <ul class="nav navbar-nav hidden-xs hidden-sm">
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
                	<h3><i class="fa fa-flag"></i>Contact</h3>
                    <p class="contact-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nec lorem quis est ultrices volutpat.</p>
                    <ul class="list-unstyled contact-address">
                    	<li>212-222 Broadway, New York, NY 10038, USA</li>
                        <li><a>info@mycompany.com</a></li>
                        <li>+420 123 456 789</li>
                        <li>www.mycompany.com</li>
                    </ul>
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
                	<h3><i class="fa fa-camera"></i>Latest Work</h3>
                    <div class="row" id="latest-work-footer">
						<div class="overlay-wrapper col-sm-6">
                            <img src="image/image_01.jpg" class="img-responsive" alt="">
                            <span class="overlay">
                                <a href="portfolio-post1.html"><i class="fa fa-plus"></i></a>
                            </span>
                        </div>
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
    <script src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=true"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/owl.carousel.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/creative-brands.js"></script>
    <script src="js/color-switcher.js"></script>
    <script src="js/jquery.countTo.js"></script>
    <script src="js/jquery.countdown.js"></script>
    <script src="js/custom.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
