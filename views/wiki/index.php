<?php

use yii\helpers\Html;

/** @var $dataProvider \yii\data\ActiveDataProvider */


$this->title = 'Wiki';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="guide-header-wrap">
    <div class="container guide-header common-heading">
        <div class="row">
            <div class="col-md-12">
                <h1 class="guide-headline"><?= Html::encode($this->title) ?></h1>
            </div>
        </div>
    </div>
</div>

<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar', [
                'category' => $category,
                'sort' => $dataProvider->sort,
            ]) ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
            ]) ?>

<!-- delete from here -- >
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.

            <div class="row">
                <div class="col-md-12 col-lg-9">
                    <div class="content wiki-row">
                        <div class="suptitle">Created 17 days ago by <a href="#">AlaFalaki</a></div>
                        <h2 class="title"><a href="#">How to change GridView delete confirmation message specific controllers only?</a></h2>
                        <div class="text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                        <div class="comments"><a href="#">0 comments</a></div>
                    </div>
                </div> 
                <div class="col-md-12 col-lg-3">
                    <div class="vote-box content">
                        <div class="thumbs">
                        <span class="up">
                            <span class="votes">1</span> 
                            <a href="#"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>
                        </span>
                        <span class="down">
                            <span class="votes">0</span>
                            <a href="#"><i class="fa fa-thumbs-down" aria-hidden="true"></i></a>
                        </span>
                        </div>
                        <div class="viewed"><span>Viewed:</span> 1 452 times</div>
                        <div class="version"><span>Version:</span> 2.0</div>
                        <div class="group"><span>Group:</span> <a href="#">Tips</a></div>
                        <div class="tags"><span>Tags:</span> <a href="#">gridview</a>, <a href="#">delete</a>, <a href="#">message</a></div>
                    </div>
                </div>              
            </div>
            <div class="row">
                <div class="col-md-12 col-lg-9">
                    <div class="content wiki-row">
                        <div class="suptitle">Created 17 days ago by <a href="#">AlaFalaki</a></div>
                        <h2 class="title"><a href="#">How to change GridView delete confirmation message specific controllers only?</a></h2>
                        <div class="text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                        <div class="comments"><a href="#">0 comments</a></div>
                    </div>
                </div> 
                <div class="col-md-12 col-lg-3">
                    <div class="vote-box content">
                        <div class="thumbs">
                        <span class="up">
                            <span class="votes">1</span> 
                            <a href="#"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>
                        </span>
                        <span class="down">
                            <span class="votes">0</span>
                            <a href="#"><i class="fa fa-thumbs-down" aria-hidden="true"></i></a>
                        </span>
                        </div>
                        <div class="viewed"><span>Viewed:</span> 1 452 times</div>
                        <div class="version"><span>Version:</span> 2.0</div>
                        <div class="group"><span>Group:</span> <a href="#">Tips</a></div>
                        <div class="tags"><span>Tags:</span> <a href="#">gridview</a>, <a href="#">delete</a>, <a href="#">message</a></div>
                    </div>
                </div>              
            </div>
<!-- delete to here -->

            <nav class="wiki-pagination-holder">
              <ul class="pagination pagination-lg wiki-pagination">
                <li class="prev disabled">
                    <span><i class="fa fa-chevron-left" aria-hidden="true"></i></span>
                </li>
                <li class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li><a href="#">6</a></li>
                <li><a href="#">7</a></li>
                <li><a href="#">8</a></li>
                <li><a href="#">9</a></li>
                <li><a href="#">10</a></li>
                <li class="next">
                    <a href="#"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                </li>
              </ul>
            </nav>

        </div>
    </div>
</div>