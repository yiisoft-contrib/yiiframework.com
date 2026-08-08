<?php

use app\models\ExtensionCategory;
use app\models\ExtensionTag;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\ListView;

/** @var $dataProvider ActiveDataProvider */
/** @var $category ExtensionCategory */
/** @var $version string */
/** @var $tag ExtensionTag */


$this->title = 'Extensions';

$this->beginBlock('contentSelectors');
    echo $this->render('partials/_versions', [
        'currentVersion' => $version,
        'category' => $category->id ?? null,
        'tag' => $tag,
    ]);
$this->endBlock();

?>
<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

	        <?php if (empty($category) && empty($tag)): ?>
                <div class="extension-intro">
                    <p>
                        The Yii community has developed a great amount of extensions that
                        provide a lot of useful functionality.
                    </p>
                    <ul>
                        <li>The extensions you find here are <strong>user contributed extensions</strong>.</li>
                        <li>There is also a set of extensions maintained by the Yii team,
                            we call these <?= Html::a('official extensions', ['extension/official']) ?>.</li>
                    </ul>
                </div>
            <?php endif; ?>


            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'itemOptions' => ['class' => 'col-xs-12 col-sm-6 col-lg-4'],
                'layout' => "{summary}\n<div class=\"row\">{items}</div>\n{pager}",
            ]) ?>
        </div>

        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar', [
                'category' => $category->id ?? null,
                'tag' => $tag,
                'sort' => $dataProvider->sort,
                'version' => $version,
            ]) ?>
        </div>
    </div>
</div>
