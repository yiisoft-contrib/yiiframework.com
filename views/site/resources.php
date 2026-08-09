<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Learning Resources';
$this->params['breadcrumbs'][] = $this->title;
?>
<main class="container content learning-page style_external_links">
    <header class="learning-page__intro">
        <h1>Learning resources</h1>
        <p>Free tutorials and community-made courses for learning Yii at your own pace.</p>
    </header>

    <div class="learning-sections">
        <section class="learning-section" aria-labelledby="learning-yii3">
            <header>
                <h2 id="learning-yii3">Yii 3</h2>
                <p>Short introductions to creating and structuring a modern Yii application.</p>
            </header>
            <ul class="learning-list">
                <li>
                    <span class="learning-list__type">Video</span>
                    <a href="https://www.youtube.com/watch?v=-AY6DT2IcaM">Time to Hello World</a>
                    <span class="learning-list__description">Create a project and run your first Yii 3 application.</span>
                </li>
                <li>
                    <span class="learning-list__type">Video</span>
                    <a href="https://www.youtube.com/watch?v=NvN93QEycYU">Building a static website</a>
                    <span class="learning-list__description">Continue with a small, practical website.</span>
                </li>
            </ul>
        </section>

        <section class="learning-section" aria-labelledby="learning-yii2">
            <header>
                <h2 id="learning-yii2">Yii 2</h2>
                <p>Long-form courses and established community video series.</p>
            </header>
            <ul class="learning-list">
                <li>
                    <span class="learning-list__type">Course</span>
                    <a href="https://www.youtube.com/watch?v=whuIf33v2Ug">Full course: build a YouTube clone</a>
                    <span class="learning-list__description">A complete project-based introduction to Yii 2.</span>
                </li>
                <li>
                    <span class="learning-list__type">Video series</span>
                    <a href="https://www.youtube.com/playlist?list=PLMyGpiUTm106xkNQh9WeMsa-LXjanaLUm">Beginning Yii 2.0</a>
                    <span class="learning-list__description">Beginner series by Tom King.</span>
                </li>
                <li>
                    <span class="learning-list__type">Video series</span>
                    <a href="https://www.youtube.com/playlist?list=PLRd0zhQj3CBmusDbBzFgg3H20VxLx2mkF">Yii 2 lessons</a>
                    <span class="learning-list__description">Community series by DoingITeasyChannel.</span>
                </li>
                <li>
                    <span class="learning-list__type">Video series</span>
                    <a href="https://www.youtube.com/watch?v=aq0A2o6nGuA&amp;list=PLpNYlUeSK_rn_3mWq_vPt_jKz6cp7a6sZ">Yii 2.0 series</a>
                    <span class="learning-list__description">Community series by Luke Briner.</span>
                </li>
            </ul>
        </section>

        <section class="learning-section" aria-labelledby="learning-yii11">
            <header>
                <h2 id="learning-yii11">Yii 1.1</h2>
                <p>The original official project tutorial, retained for Yii 1.1 applications.</p>
            </header>
            <ul class="learning-list">
                <li>
                    <span class="learning-list__type">Tutorial</span>
                    <?= Html::a('Building a blog system', ['guide/blog-entry']) ?>
                    <span class="learning-list__description">Build a complete blog application step by step.</span>
                </li>
            </ul>
        </section>
    </div>
</main>
