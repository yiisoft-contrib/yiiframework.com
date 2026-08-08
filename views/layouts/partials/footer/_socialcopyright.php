<?php
use yii\helpers\Html;
?>
<ul class="footerList">
  <li class="footerList_item">
      <span class="social">
          <a href="https://x.com/yiiframework" aria-label="X Account" target="_blank" rel="noopener noreferrer">
              <svg class="social-x-icon" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M15.75.96h3.067l-6.7 7.66L20 19.037h-6.172l-4.833-6.32-5.532 6.32H.395l7.167-8.191L0 .962h6.328l4.37 5.776L15.75.961Zm-1.075 16.243h1.7L5.404 2.7H3.582l11.093 14.503Z"/>
              </svg>
          </a>
          <a href="https://t.me/yii3en" aria-label="Telegram Group" target="_blank" rel="noopener noreferrer">
              <svg class="social-telegram-icon" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M18.384 2.879 1.413 9.399c-1.159.421-1.152 1.1-.212 1.384l4.357 1.36 10.096-6.37c.476-.289.909-.134.553.184l-8.184 7.391-.306 4.357c.448 0 .646-.206.896-.449l2.151-2.089 4.468 3.301c.825.453 1.418.22 1.623-.766l2.937-13.844c.171-1.414-.593-1.787-1.394-1.4Z"/>
              </svg>
          </a>
          <?= Html::a(
              '<svg class="social-rss-icon" viewBox="0 0 20 20" aria-hidden="true">'
              . '<path d="M3 2a1 1 0 0 0 0 2c7.18 0 13 5.82 13 13a1 1 0 1 0 2 0C18 8.716 11.284 2 3 2Zm0 5a1 1 0 0 0 0 2 8 8 0 0 1 8 8 1 1 0 1 0 2 0A10 10 0 0 0 3 7Zm2 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>'
              . '</svg>',
              ['rss/all'],
              ['aria-label' => 'RSS Feed']
          ) ?>
      </span>
  </li>
  <li class="footerList_item">
    <?= Html::a('Terms of service', ['site/tos']) ?>
  </li>
  <li class="footerList_item">
      <?= Html::a('License', ['site/license']) ?>
  </li>
  <li class="footerList_item">
      <?= Html::a('Contact Us', ['site/contact']) ?>
  </li>
</ul>
