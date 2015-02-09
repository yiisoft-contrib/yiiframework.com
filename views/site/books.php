<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-books">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>There are handy books about both Yii 2.0 and Yii 1.1.</p>

    <h2>Yii 2.0</h2>

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <a href="https://leanpub.com/yii2forbeginners"><img src="https://s3.amazonaws.com/titlepages.leanpub.com/yii2forbeginners/large?1416032860" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-3">
            <a href="http://www.amazon.com/dp/1783981881?tag=gii20f-20"><img src="http://ecx.images-amazon.com/images/I/51YWA8WvU2L.jpg" class="img-responsive" /></a>
        </div>
    </div>


    <h2>Yii 1.0</h2>

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <a href="http://www.amazon.com/dp/178328773X?tag=gii20f-20"><img alt="Yii Project Blueprints" src="http://ecx.images-amazon.com/images/I/514Ft5H%2BmZL.jpg" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-3">
            <a href="http://yiicookbook.org/"><img alt="Yii Application Development Cookbook" src="http://ecx.images-amazon.com/images/I/51wKCp3gr1L.jpg" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-3">
            <a href="http://www.packtpub.com/yii-rapid-application-development-hotshot/book"><img alt="Yii Rapid Application Development Hotshot" src="https://www.packtpub.com/sites/default/files/7508OS.jpg" class="img-responsive" /></a>
        </div>

        <div class="col-sm-6 col-md-3">
            <a href="http://www.seesawlabs.com/yii-book"><img alt="Web Application Development with Yii and PHP" src="http://ecx.images-amazon.com/images/I/5178uFKKD0L.jpg" class="img-responsive" /></a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <a href="http://yii.larryullman.com/"><img alt="The Yii Book" src="https://larry.pub/images/yiibookcover.png" class="img-responsive" /></a>
        </div>
    </div>

    <h2>Recommended reads</h2>

    <p>Besides the Yii books, we also recommend the following books for improving your PHP and Web development skills:</p>

    <ul>
        <li>Joe Celko's SQL for Smarties: Advanced SQL Programming Third Edition</li>
        <li>Design Patterns: Elements of Reusable Object-Oriented Software</li>
    </ul>
</div>
