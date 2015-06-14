<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Getting started';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contribute content container">
    <h1><?= Html::encode($this->title) ?></h1>


    <ol>
        <li><?= Html::a('Read "Getting started" guide', ['guide/view', 'section' => 'start-installation', 'language' => 'en', 'version' => key(Yii::$app->params['guide.versions'])]) ?>.</li>
        <li>To learn more, <?= Html::a('read the Guide', ['guide/entry']) ?>.</li>
        <li>Get to know the <?= Html::a('API docs', ['api/index', 'version' => reset(Yii::$app->params['api.versions'])])?>. you'll use them every day (at least for a while). You can view Yii source code directly in the API docs. The API search box is invaluable.</li>
    </ol>

    <h2>To get a full understanding of the Yii Framework</h2>

    <p>Check this <a href="http://www.yiiframework.com/wiki/268/how-to-learn-yii">great wiki article written by Yii community</a>.</p>
</div>
