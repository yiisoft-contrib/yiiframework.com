<?php
/**
 * @var yii\web\View $this
 */

use yii\helpers\Html;

$this->title = 'Extensions';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="guide-header-wrap">
    <div class="container guide-header common-heading">
        <div class="row">
            <div class="col-md-12">
                <h1 class="guide-headline"><?= Html::encode($this->title) ?></h1>
                <small>via packagist.org</small>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="content">
        <?= \app\widgets\Alert::widget() ?>
    </div>
</div>