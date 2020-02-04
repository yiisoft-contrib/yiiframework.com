<?php
/* @var $books1 array */
/* @var $books2 array */
/* @var $this yii\web\View */
$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container site-header">
	<div class="row">
		<div class="col-7 col-sm-9 col-md-7">
            <h1>Books,</h1>
            <h2>that help you master the framework</h2>
		</div>
		<div class="col-5 col-sm-3 col-md-5">
			<img class="background" src="<?= Yii::getAlias('@web/image/books/header.svg')?>" alt="">
		</div>
	</div>
</div>

<div class="container content books">
    <div class="version">
        <h2><span>Yii 2.0</span></h2>
    </div>
    <div class="row">
        <?= $this->render('partials/_books', ['books' => $books2]) ?>
    </div>
    <div class="version">
        <h2><span>Yii 1.1</span></h2>
    </div>
    <div class="row">
        <?= $this->render('partials/_books', ['books' => $books1]) ?>
    </div>
</div>
