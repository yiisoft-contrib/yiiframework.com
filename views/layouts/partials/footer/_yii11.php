<?php
use yii\helpers\Html;
?>
<ul class="footerList">
    <li class="footerList_item">
        <div>Yii 1.1</div>
    </li>
    <li class="footerList_item">
        <?= Html::a('Guide', ['guide/index', 'type' => 'guide', 'version' => '1.1', 'language' => 'en']) ?>
    </li>
    <li class="footerList_item">
        <?= Html::a('API', ['api/index', 'version' => '1.1']) ?>
    </li>
    <li class="footerList_item">
        <?= Html::a('Wiki', ['wiki/index', 'version' => '1.1']) ?>
    </li>
</ul>
