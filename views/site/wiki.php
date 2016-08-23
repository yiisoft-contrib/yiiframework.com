<?php

use yii\helpers\Html;

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
        <div class="col-sm-2 col-md-2 col-lg-2">
            <a href="#" class="btn btn-block btn-new-wiki-article"><span class="big">Write</span><span class="small">new article</span></a>
            <input type="text" class="form-control wiki-search" id="search" name="q" placeholder="Search wiki…" autocomplete="off" value="">

            <h3 class="wiki-side-title">Sorting by</h3>
            <ul class="wiki-side-menu">
                <li class="active"><a href="#">Date</a></li>
                <li><a href="#">Rating</a></li>
                <li><a href="#">Comments</a></li>
                <li><a href="#">Views</a></li>
            </ul>

            <h3 class="wiki-side-title">Categories</h3>
            <ul class="wiki-side-menu">
                <li class="active"><a href="#">All</a></li>
                <li><a href="#">Tips <span class="count">(168)</span></a></li>
                <li><a href="#">How-tos <span class="count">(367)</span></a></li>
                <li><a href="#">Tutorials <span class="count">(266)</span></a></li>
                <li><a href="#">FAQs <span class="count">(9)</span></a></li>
                <li><a href="#">Others <span class="count">(26)</span></a></li>
            </ul>

            <h3 class="wiki-side-title">Popular Tags</h3>
            <ul class="wiki-side-menu last-side-menu">
                <li><a href="#">Ajax <span class="count">(52)</span></a></li>
                <li><a href="#">Authentification <span class="count">(23)</span></a></li>
            </ul>
        </div>

        <div class="col-sm-10 col-md-10 col-lg-10" role="main">
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

            <nav aria-label="Page navigation">
              <ul class="pagination pagination-lg wiki-pagination">
                <li>
                  <a href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                <li class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li>
                  <a href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              </ul>
            </nav>

        </div>
</div>
</div>