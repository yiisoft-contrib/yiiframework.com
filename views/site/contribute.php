<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Contribute';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container site-header">
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <h1><?= $this->title ?></h1>
            <h2>There are several ways you can help</h2>
        </div>
        <div class="col-md-4 col-sm-4">
            <img class="background" src="<?= Yii::getAlias('@web/image/contribute/contribute.svg')?>" alt="">
        </div>
    </div>
</div>
<div class="container contribute">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="heading-separator">
                    <h2><span>Spread the word</span></h2>
                </div>
                <p class="text-center"><img class="icon" src="<?= Yii::getAlias('@web/image/contribute/ico_contribute_01.svg')?>" alt=""></p>
                <p class="text-center">You like Yii? Built an outstanding website or application? Blog about it, tweet about it,
                    and <a href="https://yiipowered.com/en">add it to YiiPowered</a>.
                </p>
                <p class="text-center">Have news to share? Add them to <a href="https://yiifeed.com/">YiiFeed</a>.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="heading-separator">
                            <h2><span>Report bugs</span></h2>
                        </div>
                        <p class="text-center"><img class="icon" src="<?= Yii::getAlias('@web/image/contribute/ico_contribute_02.svg')?>" alt=""></p>
                        <p>
                            Found a bug? We probably don't know about it yet so you can help us
                            reporting it in one of the <?= Html::a('Yii issue trackers', ['site/report-issue']) ?>.
                        </p>
                        <p>
                            Please use the <?= Html::a('contact form', ['site/security']) ?> to report any security issues.
                            <strong>Do not</strong> use the issue tracker or discuss it in the public forum.
                        </p>
                        <p>To help us solve issues more efficiently please:</p>
                        <ul>
                            <li>Check if the issue is not already reported.</li>
                            <li>Don't use issue tracker to ask questions. There is a
                                <a href="https://forum.yiiframework.com/">forum</a>
                                and a <?= Html::a('chat', ['site/chat']) ?>.
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <div class="heading-separator">
                            <h2><span>Help us to fix bugs</span></h2>
                        </div>
                        <p class="text-center"><img class="icon" src="<?= Yii::getAlias('@web/image/contribute/ico_contribute_03.svg')?>" alt=""></p>
                        <p>The most time consuming part of fixing a bug is reproducing it. If
                            you have some free time and want to dive into Yii internals fixing a bug,
                            the first step is to create a simple test case that contains the minimal code
                            to show the problem. Even better if you can convert it to
                            a unit test. If you don't know how to fix the bug, it's OK. By reproducing
                            it you're doing a very good job already.
                        </p>
                        <p>If you've tried hard but still couldn't come up with the minimal code to
                            reproduce the bug, it's fine too. Please describe it with details that
                            may be helpful for us to reproduce the bug: the Yii version, the PHP version, the Web
                            server type, the Web browser type, the application configuration, the error
                            call stack, the SQL statement being executed, and so on.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <blockquote class="note">Note that if you've found a security issue, it's better to
                            <?= Html::a('contact core team privately', ['site/security']); ?>.
                            We'll review the issue and will respond via email.
                        </blockquote>
                    </div>
                    <div class="col-md-6">
                        <blockquote class="note">Before you attempt to fix some non-trivial things, please
                            discuss with Yii core developers first to avoid going in the wrong direction.
                        </blockquote>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="heading-separator">
                            <h2><span>Review code</span></h2>
                        </div>
                        <p class="text-center"><img class="icon" src="<?= Yii::getAlias('@web/image/contribute/ico_contribute_04.svg')?>" alt=""></p>
                        <p>People do make mistakes. We do as well. If you're living on a cutting
                            edge and interested in all new features and bug fixes that will be
                            included in the next release, you probably will like reviewing
                            <a href="https://github.com/yiisoft/yii2/commits/master">changes we're making</a>.
                            Don't hesitate to comment either when everything is fine or when it looks
                            like core developers have gone mad. Be bold.
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="heading-separator">
                            <h2><span>Request features</span></h2>
                        </div>
                        <p class="text-center"><img class="icon" src="<?= Yii::getAlias('@web/image/contribute/ico_contribute_05.svg')?>" alt=""></p>
                        <p>Have a brilliant idea on how to improve Yii? Let us know. You can
                            request features in
                            <a href="https://github.com/yiisoft/yii-core">Yii issue tracker</a>.
                        </p>
                        <p>When requesting a feature please:</p>
                        <ul>
                            <li>Describe the problem, the feature solves, clearly. Explain why you need
                                this and what it is exactly.
                            </li>
                            <li>Suggest on how it should be implemented if you have an idea.</li>
                            <li>Provide links to existing implementations if any.</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <div class="heading-separator">
                            <h2><span>Write tests</span></h2>
                        </div>
                        <p class="text-center"><img class="icon" src="<?= Yii::getAlias('@web/image/contribute/ico_contribute_06.svg')?>" alt=""></p>
                        <p>To make Yii even more stable, you can contribute tests. Existing
                            unit tests aren't coming with Yii release packages but are
                            <a href="https://github.com/yiisoft/yii2/tree/master/tests">available from GitHub</a>.
                        </p>
                        <p>To learn about the testing framework used in Yii, please refer to the
                            <?= Html::a('Definitive Guide', ['guide/view', 'section' => 'test-overview', 'version' => '2.0', 'language' => 'en', 'type' => 'guide']) ?>.
                        </p>
                        <p>Not sure how it should work? Don't hesitate to contact the core team.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="heading-separator">
                            <h2><span>Documentation</span></h2>
                        </div>
                        <p class="text-center"><img class="icon" src="<?= Yii::getAlias('@web/image/contribute/ico_contribute_07.svg')?>" alt=""></p>
                        <p>Found a typo, wrong or unclear wording? Know how to explain things
                            better? Have a good code example or some missing documentation? You can
                            submit all these in
                            <a href="https://github.com/yiisoft/yii2/issues">Yii issue tracker</a>.
                        </p>
                        <p>Also each page in the <?= Html::a('Definitive Guide', ['guide/entry']) ?> has an edit link
                        on the bottom, that lets you update the file and submit your changes directly via Github.</p>
                    </div>
                    <div class="col-md-4">
                        <div class="heading-separator">
                            <h2><span>Translate</span></h2>
                        </div>
                        <p class="text-center"><img class="icon" src="<?= Yii::getAlias('@web/image/contribute/ico_contribute_08.svg')?>" alt=""></p>
                        <p>Yii documentation and messages have been translated into many languages.
                            You may help us to keep these translations up-to-date, or translate those that
                            have not been translated yet. Please submit a GitHub pull request after
                            your translation is ready.
                        </p>
                        <p>Instructions on how to do the translation work are given in
                            the <a href="https://github.com/yiisoft/yii2/blob/master/docs/internals/translation-workflow.md">Translation workflow</a> at GitHub.
                            Also if you need help, we are happy to help you get started, just ask.
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="heading-separator">
                            <h2><span>Donate</span></h2>
                        </div>
                        <p class="text-center"><img class="icon" src="<?= Yii::getAlias('@web/image/contribute/ico_contribute_05.svg')?>" alt=""></p>
                        <p>
                            Last but not least, there is an option to
                            <a href="<?= Url::to('donate') ?>">fund Yii development</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
