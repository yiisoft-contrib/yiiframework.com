<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Getting started';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
            <p>
                Introductory notes here..
            </p>
            <ul class="timeline">
                <li>
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="http://lorempixel.com/250/250/cats/1" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Step One</h4>
                            <h4 class="subheading">Subtitle</h4>
                        </div>
                        <div class="timeline-body">
                            <p class="text-muted">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                        </div>
                    </div>
                    <div class="line"></div>
                </li>
                <li class="timeline-inverted">
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="http://lorempixel.com/250/250/cats/2" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Step Two</h4>
                            <h4 class="subheading">Subtitle</h4>
                        </div>
                        <div class="timeline-body">
                            <p class="text-muted">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                        </div>
                    </div>
                    <div class="line"></div>
                </li>
                <li>
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="http://lorempixel.com/250/250/cats/3" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Step Three</h4>
                            <h4 class="subheading">Subtitle</h4>
                        </div>
                        <div class="timeline-body">
                            <p class="text-muted">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                        </div>
                    </div>
                    <div class="line"></div>
                </li>
                <li class="timeline-inverted">
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="http://lorempixel.com/250/250/cats/4" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Step Three</h4>
                            <h4 class="subheading">Subtitle</h4>
                        </div>
                        <div class="timeline-body">
                            <p class="text-muted">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                        </div>
                    </div>
                    <div class="line"></div>
                </li>
                <li>
                    <div class="timeline-image">
                        <img class="img-circle img-responsive" src="http://lorempixel.com/250/250/cats/5" alt="">
                    </div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4>Bonus Step</h4>
                            <h4 class="subheading">Subtitle</h4>
                        </div>
                        <div class="timeline-body">
                            <p class="text-muted">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2>lskdfjslafj</h2>
            <ol>
                <li><?= Html::a('Read "Getting started" guide', ['guide/view', 'section' => 'start-installation', 'language' => 'en', 'version' => key(Yii::$app->params['guide.versions']), 'type' => 'guide']) ?>.</li>
                <li>To learn more, <?= Html::a('read the Guide', ['guide/entry']) ?>.</li>
                <li>Get to know the <?= Html::a('API docs', ['api/index', 'version' => reset(Yii::$app->params['api.versions'])])?>. you'll use them every day (at least for a while). You can view Yii source code directly in the API docs. The API search box is invaluable.</li>
            </ol>

            <p>Check this <a href="http://www.yiiframework.com/wiki/268/how-to-learn-yii">great wiki article written by Yii community</a>.</p>
        </div>
    </div>
</div>
