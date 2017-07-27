<?php
use yii\helpers\Html;
?>
<ul class="footerList">
  <li class="footerList_item">
    <div>Documentation</div>
  </li>
  <li class="footerList_item">
    <?= Html::a('Guide', ['guide/entry']) ?>
  </li>
  <li class="footerList_item">
    <?= Html::a('API', ['api/entry']) ?>
  </li>
<?php /*
  <li class="footerList_item">
    <?= Html::a('Yii Tour', ['site/tour']) ?>
  </li>
*/ ?>
  <li class="footerList_item">
    <?= Html::a('Wiki', ['wiki/index']) ?>
  </li>
  <li class="footerList_item">
    <?= Html::a('Resources', ['site/resources']) ?>
  </li>
</ul>
