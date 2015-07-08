<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-books content container">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>There are handy books about both Yii 2.0 and Yii 1.1 which could help you master the framework.</p>

    <h2>Yii 2.0</h2>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <a href="http://yii.larryullman.com/"><img alt="The Yii Book" src="<?= Yii::getAlias('@web/image/books/yii1-yiibook-larry.png') ?>" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>The Yii Book (second edition) <small class="authors">by Larry Ullman</small></h3>

            <h4>Recommended for beginners.</h4>

            <p>The book starts with the very basics: object oriented programming, MVC, using a web server and command line tools.
            Then gradually shows how to use Yii. It covers everything from the very basics such as installing Yii to advanced topics
            such as implementing your own framework extension. Explanations are very clear. Additionally to the guide-style
                chapters there are two complete example chapters: a CMS and an E-commerce website.
            Note that the second edition (for Yii 2.0) is <strong>not finished yet</strong>, but buyers can download updates as soon as they are available.
            </p>
        </div>
    </div>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <a href="https://leanpub.com/yii2forbeginners"><img src="<?= Yii::getAlias('@web/image/books/yii2-forbeginners.png') ?>" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>PHP Web Application Development <small class="authors">by Bill Keck</small></h3>

            <h4>Recommended for beginners.</h4>

            <p>It is a step by step introduction to the framework, which is based around
                creating a reusable template that can serve as the basis for your projects.</p>
        </div>
    </div>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <a href="http://www.amazon.com/dp/1783981881?tag=gii20f-20"><img src="<?= Yii::getAlias('@web/image/books/yii2-app-development.png') ?>" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>Web Application Development with Yii 2 and PHP <small class="authors">by Mark Safronov</small></h3>

            <h4>Recommended for intermediate-to-advanced developers.</h4>

            <p>The book is based around implementing a real world CRM application following many development best practices.
            It is assumed that reader is experienced enough in object orientented programming so don't expect OO-basics
            explained. There are many references to good overall programming books so even if you're already familiar with
            Yii 2.0 it worth at least skimming it.</p>
        </div>
    </div>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <a href="http://www.amazon.com/dp/1784392278?tag=gii20f-20"><img src="<?= Yii::getAlias('@web/image/books/yii2-testing.png') ?>" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>Learning Yii Testing <small class="authors">by Matteo Pescarin</small></h3>

            <h4>Recommended for intermediate-to-advanced developers.</h4>

            <p>Embrace 360-degree testing on your Yii 2 projects using Codeception.</p>
        </div>
    </div>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <div class="simple">
                <a href="https://github.com/samdark/yii2-cookbook">Yii 2.0 Cookbook</a>
            </div>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>Yii 2.0 Cookbook <small class="authors">by Alexander Makarov</small></h3>

            <h4>Work in progress OpenSource Yii 2.0 recipe book.</h4>

            <p>Although not finished, there are some nice recipes from one of the core team and community. Worth checking.</p>
        </div>
    </div>

    <h2>Yii 1.1</h2>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <a href="http://yii.larryullman.com/"><img alt="The Yii Book" src="<?= Yii::getAlias('@web/image/books/yii1-yiibook-larry.png') ?>" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>The Yii Book (first edition) <small class="authors">by Larry Ullman</small></h3>

            <h4>Recommended for beginners.</h4>

            <p>The book starts with the very basics: object oriented programming, MVC, using a web server and command line tools.
            Then gradually shows how to use Yii. It covers everything from the very basics such as installing Yii to advanced topics
            such as implementing your own framework extension. Explanations are very clear. Additionally to the guide-style
                chapters there are two complete example chapters: a CMS and an E-commerce website.</p>
        </div>
    </div>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <a href="http://yiicookbook.org/"><img alt="Yii Application Development Cookbook" src="<?= Yii::getAlias('@web/image/books/yii1-cookbook.png') ?>" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>Yii Application Developement Cookbook <small class="authors">by Alexander Makarov</small></h3>

            <h4>Recommended for intermediate-to-advanced developers.</h4>

            <p>The book is a set of individual independent recipes written by Yii core developer. Each recipe shows how to
            do something useful with Yii explainig why it's done, how it's done, why it works and how exactly it works.</p>
        </div>
    </div>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <a href="http://www.seesawlabs.com/yii-book"><img alt="Web Application Development with Yii and PHP" src="<?= Yii::getAlias('@web/image/books/yii1-app-development.png') ?>" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>Web Application Development with Yii and PHP <small class="authors">by Jeffrey Winesett</small></h3>

            <h4>Recommended for beginners.</h4>

            <p>Written by former Yii core developer, the book takes a strong learn-by-doing philosophy to introducing you
               to the Yii framework. After a brief introduction to the framework in chapters 1 and 2, the remaining 10
               chapters are dedicated to building an entire project managemnt application.</p>
        </div>
    </div>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <a href="http://www.amazon.com/dp/178328773X?tag=gii20f-20"><img alt="Yii Project Blueprints" src="<?= Yii::getAlias('@web/image/books/yii1-blueprints.png') ?>" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>Yii Project Blueprints <small class="authors">by Charles R. Portwood II</small></h3>

            <h4>Recommended for intermediate-to-advanced Yii developers.</h4>

            <p>This book is a step-by-step guide to developing reusable real-world applications using the Yii framework.
               The book will guide you through several projects from the project conception through to planning your
               project and implementation.</p>
        </div>
    </div>

    <div class="row book">
        <div class="col-sm-6 col-md-3">
            <a href="http://www.packtpub.com/yii-rapid-application-development-hotshot/book"><img alt="Yii Rapid Application Development Hotshot" src="<?= Yii::getAlias('@web/image/books/yii1-hotshot.png') ?>" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-9">
            <h3>Yii Rapid Application Developement <small class="authors">by Lauren J. O'Meara, James R. Hamilton III</small></h3>

            <h4>Recommended for intermediate-to-advanced Yii developers.</h4>

            <p>The book is by-example guide to framework based around building example projects and explaining what was
            done in the process.</p>
        </div>
    </div>
</div>
