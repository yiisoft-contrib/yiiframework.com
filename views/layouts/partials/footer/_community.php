<?php

use yii\helpers\Html;

?>
<ul class="footerList">
    <li class="footerList_item">
        <div>Community</div>
    </li>
    <li class="footerList_item">
        <?= Html::a('Forum', '@web/forum') ?>
    </li>
    <li class="footerList_item">
        <?= Html::a('Live Chat', ['site/chat']) ?>
    </li>
<?php /*
    <li class="footerList_item">
        <a href="#">Gitter</a>
    </li>
 */ ?>
    <li class="footerList_item">
        <a href="https://www.facebook.com/groups/yiitalk/" target="_blank" rel="noopener noreferrer">Facebook Group</a>
    </li>
    <li class="footerList_item">
        <?= Html::a('Hall of Fame', ['/user/halloffame']) ?>
    </li>
    <li class="footerList_item">
        <?= Html::a('Badges', ['/badges']) ?>
    </li>
</ul>
