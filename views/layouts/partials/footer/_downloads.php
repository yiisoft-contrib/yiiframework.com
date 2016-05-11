<?php
use yii\helpers\Html;
?>
<ul class="footerList">
  <li class="footerList_item">
    <div>Downloads</div>
  </li>
  <li class="footerList_item">
    <?= Html::a('Framework', ['site/download']) ?>
  </li>
  <li class="footerList_item">
    <?= Html::a('Documentation', ['site/download', '#' => 'offline-documentation']) ?>
  </li>
  <li class="footerList_item">
    <a href="#">Extensions</a>
  </li>
  <li class="footerList_item">
    <?= Html::a('Logo', ['site/logo']) ?>
  </li>
</ul>
