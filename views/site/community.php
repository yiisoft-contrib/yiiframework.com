<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<main class="container community-page style_external_links">
    <header class="community-page__header">
        <h1>Community Places</h1>
        <p>
            Meet other Yii developers, ask questions, and join conversations in your preferred language.
            These spaces are maintained by both the Yii team and the wider community.
        </p>
    </header>

    <section class="community-section" aria-labelledby="global-community-title">
        <h2 id="global-community-title">Global community</h2>

        <div class="community-directory">
            <article class="community-group" id="chats">
                <h3>Real-time chat</h3>
                <p>Chat is community-supported. Please be patient and give people time to respond.</p>
                <ul>
                    <li>
                        <a href="https://t.me/yii_framework_in_english">Yii 2 Telegram</a>
                    </li>
                    <li><a href="https://t.me/yii3en">Yii 3 Telegram</a></li>
                    <li>
                        <a href="<?= Html::encode(Url::to(['go/slack'])) ?>">Yii Slack</a>
                    </li>
                    <li>
                        <a href="ircs://irc.libera.chat:6697/yii">#yii on Libera Chat</a>
                        <span><a href="https://web.libera.chat/">Web client</a></span>
                    </li>
                </ul>
            </article>

            <article class="community-group">
                <h3>Questions and answers</h3>
                <p>Search existing answers or ask a focused technical question.</p>
                <ul>
                    <li><a href="<?= Yii::getAlias('@web/forum') ?>">Official forum</a></li>
                    <li><a href="https://github.com/yiisoft/yii2/discussions">Yii 2 GitHub Discussions</a></li>
                    <li><a href="https://stackoverflow.com/questions/tagged/yii">Yii 1.1 on Stack Overflow</a></li>
                    <li><a href="https://stackoverflow.com/questions/tagged/yii2">Yii 2 on Stack Overflow</a></li>
                </ul>
            </article>

            <article class="community-group">
                <h3>Social</h3>
                <p>Follow project updates and connect with the broader community.</p>
                <ul>
                    <li><a href="https://x.com/yiiframework">Yii on X</a></li>
                    <li><a href="https://www.facebook.com/groups/yiitalk/">Facebook</a></li>
                    <li><a href="https://www.linkedin.com/groups/1483367/">LinkedIn</a></li>
                    <li><a href="https://www.reddit.com/r/yii/">r/yii</a></li>
                    <li><a href="https://www.reddit.com/r/PHP/">r/PHP</a></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="community-section" aria-labelledby="regional-community-title">
        <h2 id="regional-community-title">Regional communities</h2>

        <div class="community-regions">
            <article class="community-region">
                <div class="community-region__content">
                    <h3>Russian and Ukrainian</h3>

                    <h4>Websites and events</h4>
                    <ul>
                        <li><a href="https://yiiframework.ru/">yiiframework.ru</a></li>
                        <li><a href="https://yiiframework.com.ua/">yiiframework.com.ua</a></li>
                        <li><a href="https://habrahabr.ru/hub/yii/">Yii on Habr</a></li>
                        <li><a href="https://yiiconf.ru/">YiiConf Russia</a></li>
                    </ul>

                    <h4>Telegram</h4>
                    <ul>
                        <li><a href="https://t.me/yii1ru">Yii 1.1</a></li>
                        <li><a href="https://t.me/yii2ru">Yii 2</a></li>
                        <li><a href="https://t.me/yii3ru">Yii 3</a></li>
                        <li><a href="https://t.me/yii_jobs">Jobs</a></li>
                    </ul>

                    <h4>Questions and social</h4>
                    <ul>
                        <li><a href="https://ru.stackoverflow.com/questions/tagged/yii">Yii on Stack Overflow на русском</a></li>
                        <li><a href="https://ru.stackoverflow.com/questions/tagged/yii2">Yii 2 on Stack Overflow на русском</a></li>
                        <li><a href="https://qna.habr.com/tag/yii/questions">Habr Q&amp;A</a></li>
                        <li><a href="https://vk.com/yiiframework">VK Russia</a></li>
                        <li><a href="https://vk.com/yiiframework_ua">VK Ukraine</a></li>
                        <li><a href="https://www.facebook.com/groups/yiitalk.ru/">Facebook</a></li>
                    </ul>
                </div>
            </article>

            <article class="community-region">
                <h3>Farsi</h3>
                <ul>
                    <li><a href="https://t.me/yii_framework_in_farsi">Telegram</a></li>
                </ul>
            </article>

            <article class="community-region">
                <h3>Uzbek</h3>
                <ul>
                    <li><a href="https://t.me/yiiframework_uz">Telegram</a></li>
                </ul>
            </article>

            <article class="community-region">
                <h3>Indonesian</h3>
                <ul>
                    <li><a href="https://t.me/YiiFrameworkIndonesia">Telegram</a></li>
                    <li><a href="https://www.facebook.com/groups/yii.indonesia/">Facebook</a></li>
                </ul>
            </article>
        </div>
    </section>

    <p class="community-resource-contribution">
        Missing a community place?
        <a href="https://github.com/yiisoft-contrib/yiiframework.com/issues/new" target="_blank" rel="noopener noreferrer">
            <svg viewBox="0 0 20 20" aria-hidden="true">
                <path d="M10 .248c-5.525 0-10 4.477-10 10a9.998 9.998 0 0 0 6.838 9.487c.5.094.683-.215.683-.48 0-.238-.008-.867-.013-1.7-2.781.602-3.368-1.343-3.368-1.343-.455-1.154-1.112-1.462-1.112-1.462-.906-.62.07-.607.07-.607 1.004.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.913.831.09-.647.347-1.087.633-1.337-2.22-.25-4.555-1.11-4.555-4.942 0-1.092.388-1.983 1.03-2.683-.113-.253-.45-1.27.087-2.647 0 0 .837-.268 2.75 1.025.8-.223 1.65-.332 2.5-.338.85.006 1.7.115 2.5.338 1.9-1.293 2.737-1.025 2.737-1.025.538 1.378.2 2.394.1 2.647.638.7 1.025 1.591 1.025 2.683 0 3.842-2.337 4.688-4.562 4.933.35.3.675.914.675 1.85 0 1.339-.013 2.414-.013 2.739 0 .262.175.575.688.475A9.966 9.966 0 0 0 20 10.247c0-5.522-4.477-10-10-10Z"/>
            </svg>
            <span>Create an issue</span>
        </a>.
    </p>
</main>
