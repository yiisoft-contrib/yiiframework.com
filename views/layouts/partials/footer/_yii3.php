<?php
use yii\helpers\Html;
?>
<ul class="footerList">
    <li class="footerList_item">
        <div>Yii3</div>
    </li>
    <li class="footerList_item">
        <?= Html::a('Guide', 'https://yiisoft.github.io/docs/guide/', ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?>
    </li>
    <li class="footerList_item">
        <?= Html::a('API', ['api/index', 'version' => '3.0']) ?>
    </li>
    <li class="footerList_item">
        <?= Html::a('Cookbook', 'https://yiisoft.github.io/docs/cookbook/', ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?>
    </li>
</ul>
