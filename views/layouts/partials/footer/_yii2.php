<?php
use yii\helpers\Html;
?>
<ul class="footerList">
    <li class="footerList_item">
        <div>Yii 2</div>
    </li>
    <li class="footerList_item">
        <?= Html::a('Guide', ['guide/index', 'type' => 'guide', 'version' => '2.0', 'language' => 'en']) ?>
    </li>
    <li class="footerList_item">
        <?= Html::a('API', ['api/index', 'version' => '2.0']) ?>
    </li>
    <li class="footerList_item">
        <?= Html::a('Wiki', ['wiki/index', 'version' => '2.0']) ?>
    </li>
</ul>
