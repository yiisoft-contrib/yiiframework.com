<?php
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $title string  */
/** @var $menu array */
?>
<div class="guide-header-wrap">
    <div class="container admin-header common-heading">
        <div class="row">
            <div class="col-md-12">
	            <?php if (!empty($menu)) {
	                echo \yii\bootstrap\Nav::widget([
						'id' => 'admin-nav',
						'items' => $menu,
					]);
	            }
	            ?>
                <h1 class="admin-headline"><?= Html::encode($title) ?></h1>
            </div>
        </div>
    </div>
</div>
