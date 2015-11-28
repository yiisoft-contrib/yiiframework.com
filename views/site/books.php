<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="site-books">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>There are handy books about both Yii 2.0 and Yii 1.1 which could help you master the framework.</p>

            <h2>Yii 2.0</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="info-card">
                <img src="<?= Yii::getAlias('@web/image/books/yii1-yiibook-larry.png') ?>" alt="The Yii Book" />
                <div class="info-card-details animate">
                    <div class="info-card-header">
                        <h1> The Yii Book </h1>
                        <h3> Second edition </h3>
                        by Larry Ullman
                    </div>
                    <div class="info-card-detail">
                        <!-- Description -->
                        <p>
                            The book starts with the very basics: object oriented programming, MVC, using a web server and command line tools.
                            Then gradually shows how to use Yii. It covers everything from the very basics such as installing Yii to advanced topics
                            such as implementing your own framework extension. Explanations are very clear. Additionally to the guide-style
                                chapters there are two complete example chapters: a CMS and an E-commerce website.
                            Note that the second edition (for Yii 2.0) is <strong>not finished yet</strong>, but buyers can download updates as soon as they are available.
                        </p>
                    </div>
                    <div class="social">
                        <a href="http://yii.larryullman.com/">The Yii Book</a>
                        Recommended for beginners.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="[ info-card ]">
                <img src="<?= Yii::getAlias('@web/image/books/yii2-forbeginners.png') ?>" alt="PHP Web Application Development" />
                <div class="[ info-card-details ] animate">
                    <div class="[ info-card-header ]">
                        <h1> PHP Web Application Development </h1>
                        <h3> by Bill Keck </h3>
                    </div>
                    <div class="[ info-card-detail ]">
                        <!-- Description -->
                        <p>
                            It is a step by step introduction to the framework, which is based around
                                creating a reusable template that can serve as the basis for your projects.
                        </p>
                    </div>
                    <div class="social">
                    <a href="https://leanpub.com/yii2forbeginners">PHP Web Application Development</a>
                    Recommended for beginners.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="[ info-card ]">
                <img src="<?= Yii::getAlias('@web/image/books/yii2-app-development.png') ?>" alt="Web Application Development with Yii 2 and PHP" />
                <div class="[ info-card-details ] animate">
                    <div class="[ info-card-header ]">
                        <h1> Web Application Development with Yii 2 and PHP </h1>
                        <h3> by Mark Safronov </h3>
                    </div>
                    <div class="[ info-card-detail ]">
                        <!-- Description -->
                        <p>
                            The book is based around implementing a real world CRM application following many development best practices.
                            It is assumed that reader is experienced enough in object orientented programming so don't expect OO-basics
                            explained. There are many references to good overall programming books so even if you're already familiar with
                            Yii 2.0 it worth at least skimming it.
                        </p>
                    </div>
                    <div class="social">
                        <a href="http://www.amazon.com/dp/1783981881?tag=gii20f-20">Web Application Development with Yii 2 and PHP</a>
                        Recommended for intermediate-to-advanced developers.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="[ info-card ]">
                <img src="<?= Yii::getAlias('@web/image/books/yii2-testing.png') ?>" alt="Learning Yii Testing" />
                <div class="[ info-card-details ] animate">
                    <div class="[ info-card-header ]">
                        <h1> Learning Yii Testing </h1>
                        <h3> by Matteo Pescarin </h3>
                    </div>
                    <div class="[ info-card-detail ]">
                        <!-- Description -->
                        <p>
                            Embrace 360-degree testing on your Yii 2 projects using Codeception.
                        </p>
                    </div>
                    <div class="social">
                        <a href="http://www.amazon.com/dp/1784392278?tag=gii20f-20">Learning Yii Testing</a>
                        Recommended for intermediate-to-advanced developers.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="[ info-card ]">
                <img alt="Yii 2.0 Cookbook" src="/image/team/samdark.jpg" />
                <div class="[ info-card-details ] animate">
                    <div class="[ info-card-header ]">
                        <h1> Yii 2.0 Cookbook </h1>
                        <h3> by Alexander Makarov </h3>
                    </div>
                    <div class="[ info-card-detail ]">
                        <!-- Description -->
                        <p>
                            Although not finished, there are some nice recipes from one of the core team and community. Worth checking.
                        </p>
                    </div>
                    <div class="social">
                        <a href="https://github.com/samdark/yii2-cookbook">Yii 2.0 Cookbook</a>
                        Work in progress OpenSource Yii 2.0 recipe book.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
    </div>
    <div class="row">
        <h2>Yii 1.1</h2>
    </div>
    <div class="row">
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="info-card">
                <img alt="The Yii Book" src="<?= Yii::getAlias('@web/image/books/yii1-yiibook-larry.png') ?>" />
                <div class="info-card-details animate">
                    <div class="info-card-header">
                        <h1> The Yii Book (first edition) </h1>
                        <h3> by Larry Ullman </h3>
                    </div>
                    <div class="info-card-detail">
                        <!-- Description -->
                        <p>
                            The book starts with the very basics: object oriented programming, MVC, using a web server and command line tools.
                            Then gradually shows how to use Yii. It covers everything from the very basics such as installing Yii to advanced topics
                            such as implementing your own framework extension. Explanations are very clear. Additionally to the guide-style
                                chapters there are two complete example chapters: a CMS and an E-commerce website.
                        </p>
                    </div>
                    <div class="social">
                        <a href="http://yii.larryullman.com/">The Yii Book (first edition)</a>
                        Recommended for beginners.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="info-card">
                <img alt="Yii Application Development Cookbook" src="<?= Yii::getAlias('@web/image/books/yii1-cookbook.png') ?>" /></a>
                <div class="info-card-details animate">
                    <div class="info-card-header">
                        <h1> Yii Application Developement Cookbook </h1>
                        <h3> by Alexander Makarov </h3>
                    </div>
                    <div class="info-card-detail">
                        <!-- Description -->
                        <p>
                            The book is a set of individual independent recipes written by Yii core developer. Each recipe shows how to
                            do something useful with Yii explainig why it's done, how it's done, why it works and how exactly it works.
                        </p>
                    </div>
                    <div class="social">
                        <a href="http://yiicookbook.org/">Yii Application Developement Cookbook</a>
                        Recommended for intermediate-to-advanced developers.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="info-card">
                <img alt="Web Application Development with Yii and PHP" src="<?= Yii::getAlias('@web/image/books/yii1-app-development.png') ?>" />
                <div class="info-card-details animate">
                    <div class="info-card-header">
                        <h1> Web Application Development with Yii and PHP </h1>
                        <h3> by Jeffrey Winesett </h3>
                    </div>
                    <div class="info-card-detail">
                        <!-- Description -->
                        <p>
                            Written by former Yii core developer, the book takes a strong learn-by-doing philosophy to introducing you
                               to the Yii framework. After a brief introduction to the framework in chapters 1 and 2, the remaining 10
                               chapters are dedicated to building an entire project managemnt application.
                        </p>
                    </div>
                    <div class="social">
                        <a href="http://www.seesawlabs.com/yii-book">Web Application Development with Yii and PHP</a>
                        Recommended for beginners.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="info-card">
                <img alt="Yii Project Blueprints" src="<?= Yii::getAlias('@web/image/books/yii1-blueprints.png') ?>" />
                <div class="info-card-details animate">
                    <div class="info-card-header">
                        <h1> Yii Project Blueprints </h1>
                        <h3> by Charles R. Portwood II </h3>
                    </div>
                    <div class="info-card-detail">
                        <!-- Description -->
                        <p>
                            This book is a step-by-step guide to developing reusable real-world applications using the Yii framework.
                               The book will guide you through several projects from the project conception through to planning your
                               project and implementation.
                        </p>
                    </div>
                    <div class="social">
                        <a href="http://www.amazon.com/dp/178328773X?tag=gii20f-20">Yii Project Blueprints</a>
                        Recommended for intermediate-to-advanced Yii developers.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="info-card">
                <img alt="Yii Rapid Application Development Hotshot" src="<?= Yii::getAlias('@web/image/books/yii1-hotshot.png') ?>" />
                <div class="info-card-details animate">
                    <div class="info-card-header">
                        <h1> Yii Rapid Application Developement </h1>
                        <h3> by Lauren J. O'Meara, James R. Hamilton III </h3>
                    </div>
                    <div class="info-card-detail">
                        <!-- Description -->
                        <p>
                            The book is by-example guide to framework based around building example projects and explaining what was
                            done in the process.
                        </p>
                    </div>
                    <div class="social">
                        <a href="http://www.packtpub.com/yii-rapid-application-development-hotshot/book">Yii Rapid Application Developement</a>
                        Recommended for intermediate-to-advanced Yii developers.
                    </div>
                </div>
            </div>
        </div> <!-- book -->
    </div>
</div>
