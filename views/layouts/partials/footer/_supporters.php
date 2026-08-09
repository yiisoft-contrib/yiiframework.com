<?php
use yii\helpers\Html;
?>
<ul class="footerList">
    <li class="footerList_item">
        <div>Supported by</div>
    </li>
    <li class="footerList_item">
        <?= Html::a(
            '<svg viewBox="0 0 20 20" aria-hidden="true">'
            . '<path d="M10 17.4 3.1 10.8C1.7 9.5 1 8 1 6.4 1 3.9 2.9 2 5.4 2c1.5 0 3 .8 3.8 2.1L10 5.3l.8-1.2A4.6 4.6 0 0 1 14.6 2C17.1 2 19 3.9 19 6.4c0 1.6-.7 3.1-2.1 4.4L10 17.4Z"/>'
            . '</svg>Your donations',
            ['site/donate'],
            ['class' => 'footer-donations']
        ) ?>
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

