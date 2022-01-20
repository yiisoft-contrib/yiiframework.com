<?php
use yii\helpers\Html;
?>
<ul class="footerList">
  <li class="footerList_item">
      <span class="social">
          <a href="https://github.com/yiisoft" aria-label="GitHub Account" target="_blank" rel="noopener noreferrer"><i class="fa fa-github"></i></a>
          <a href="https://twitter.com/yiiframework" aria-label="Twitter Account" target="_blank" rel="noopener noreferrer"><i class="fa fa-twitter"></i></a>
          <a href="https://www.facebook.com/groups/yiitalk/" aria-label="Facebook Group" target="_blank" rel="noopener noreferrer"><i class="fa fa-facebook"></i></a>
          <?= Html::a('<i class="fa fa-rss"></i>', ['rss/all'], ['aria-label' => 'RSS Feed'])?>
      </span>
  </li>
  <li class="footerList_item">
    <?= Html::a('Terms of service', ['site/tos']) ?>
  </li>
  <li class="footerList_item">
      <?= Html::a('License', ['site/license']) ?>
  </li>
  <li class="footerList_item">
      <a href="https://github.com/yiisoft-contrib/yiiframework.com" target="_blank" rel="noopener noreferrer">Website Source Code</a>
  </li>
  <li class="footerList_item">
      &nbsp;
  </li>
  <li class="footerList_item">
    &copy; 2008 - <?= date('Y') ?> Yii
  </li>
  <li class="footerList_item">
      Design: <a href="https://www.eshill.ru/" target="_blank" rel="noopener noreferrer">Eshill</a>
  </li>
</ul>
