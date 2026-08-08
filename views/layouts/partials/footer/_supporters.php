<?php
use yii\helpers\Html;
?>
<ul class="footerList">
    <li class="footerList_item">
        <div>Supported by</div>
    </li>
    <li class="footerList_item">
        <?= Html::a('Your donations', ['site/donate']) ?>
    </li>
    <li class="footerList_item">
        <a href="https://www.jetbrains.com/?from=yii" aria-label="JetBrains website" target="_blank" rel="noopener
        noreferrer"><img
                alt="JetBrains logo" width="80" src="<?= Yii::getAlias('@web/image/jetbrains.svg') ?>"/></a>
    </li>
    <li class="footerList_item">
        &copy; 2008 - <?= date('Y') ?> Yii
        (<a href="https://github.com/yiisoft-contrib/yiiframework.com" target="_blank" rel="noopener noreferrer">Website Source Code</a>)
    </li>
</ul>


