<?php

use app\models\ExtensionCategory;
use app\models\ExtensionTag;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

/** @var $dataProvider ActiveDataProvider */
/** @var $category ExtensionCategory */
/** @var $version string */
/** @var $tag ExtensionTag */
/** @var $this View */


$this->title = 'Official Extensions';

?>
<div class="container">
    <div class="row">
        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar', [
                'category' => null,
                'tag' => null,
                'sort' => $dataProvider->sort,
                'version' => null,
                'hideCategoryAndTags' => true,
            ]) ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <h1>Official Extensions <small>Maintained by the Yii Team</small></h1>

            <div class="panel panel-default">
                <div class="panel-body">

                    <p>
                        Official Extensions are Yii extensions that are developed and reviewed
                        by Yii core <?= Html::a('team members', ['site/team'] ) ?>.<br>
                        These extensions follow the same quality standards as the framework
                        but are released independently.
                    </p>

                </div>
            </div>

            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'itemOptions' => ['class' => 'col-xs-12 col-sm-6 col-lg-4'],
                'layout' => "<div class=\"row\">{items}</div>\n{pager}",
            ]) ?>

        </div>
    </div>
</div>
