<?php
use yii\helpers\Html;
?>
<ul class="footerList">
  <li class="footerList_item">
    <div>About</div>
  </li>
  <li class="footerList_item">
    <?= Html::a('About Yii', ['guide/view', 'type' => 'guide', 'version' => reset(Yii::$app->params['versions']['api']), 'language' => 'en', 'section' => 'intro-yii']) ?>
  </li>
  <li class="footerList_item">
    <?= Html::a('News', ['news/index']) ?>
  </li>
<?php /*
  <li class="footerList_item">
    <?= Html::a('Features', ['site/features']) ?>
  </li>
  <li class="footerList_item">
    <?= Html::a('Performance', ['site/performance']) ?>
  </li>
*/ ?>
  <li class="footerList_item">
    <?= Html::a('License', ['site/license']) ?>
  </li>
  <li class="footerList_item">
    <?= Html::a('Contact Us', ['site/contact']) ?>
  </li>
</ul>
