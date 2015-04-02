<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Install Yii';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container content site-license">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Yii is an open source project released under the terms of the <?= Html::a('BSD License', ['site/license']) ?>. This means that you can use Yii for free to develop either open-source or proprietary Web applications.</p>

    <p>Currently there are two major versions of Yii: <a href="#yii2">2.0</a> and <a href="#yii1">1.1</a>.</p>

    <h2 id="yii2">Yii 2.0</h2>

    <h2 id="yii1">Yii 1.1</h2>
</div>
