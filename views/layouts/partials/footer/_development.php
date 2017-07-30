<?php
use yii\helpers\Html;
?>
<ul class="footerList">
  <li class="footerList_item">
    <div>Development</div>
  </li>
  <li class="footerList_item">
    <?= Html::a('Contribute', ['site/contribute']) ?>
  </li>
  <li class="footerList_item">
    <?= Html::a('Latest Updates', 'https://github.com/yiisoft/yii2/commits/master', [
        'target' => '_blank',
        'rel' => 'noopener noreferrer',
    ]) ?>
  </li>
  <li class="footerList_item">
    <?= Html::a('Report a Bug', ['site/report-issue']) ?>
  </li>
  <li class="footerList_item">
    <?= Html::a('Report Security Issue', ['site/security']) ?>
  </li>
</ul>
