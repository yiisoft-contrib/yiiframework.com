<?php
use yii\helpers\Html;
?>
<ul class="footerList">
  <li class="footerList_item">
      <span class="social">
          <a href="https://github.com/yiisoft"><i class="fa fa-github"></i></a>
          <a href="https://twitter.com/yiiframework"><i class="fa fa-twitter"></i></a>
          <a href="https://www.facebook.com/groups/yiitalk/"><i class="fa fa-facebook"></i></a>
          <a href="#"><i class="fa fa-rss"></i></a>
      </span>
  </li>
  <li class="footerList_item">
    <?= Html::a('Terms of service', ['site/tos']) ?>
  </li>
  <li class="footerList_item">
      <?= Html::a('License', ['site/license']) ?>
  </li>
  <li class="footerList_item">
      <a href="https://github.com/yiisoft-contrib/yiiframework.com" target="_blank">Website Souce Code</a>
  </li>
  <li class="footerList_item">
      &nbsp;
  </li>
  <li class="footerList_item">
    &copy; 2008 - <?= date('Y') ?> Yii
  </li>
  <li class="footerList_item">
      Design: <a href="http://www.eshill.ru/" target="_blank">Eshill</a>
  </li>
</ul>
