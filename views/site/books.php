<?php
/* @var $books1 array */
/* @var $books2 array */
/* @var $this yii\web\View */
$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container style_external_links">
    <main class="content books">
        <header class="books-intro">
            <h1>Books about Yii</h1>
            <p>In-depth guides and practical references written by members of the Yii community.</p>
        </header>

        <section class="books-section">
            <div class="books-section__heading">
                <h2>Yii 2</h2>
            </div>
        <?= $this->render('partials/_books', ['books' => $books2]) ?>
        </section>

        <section class="books-section">
            <div class="books-section__heading">
                <h2>Yii 1.1</h2>
            </div>
        <?= $this->render('partials/_books', ['books' => $books1]) ?>
        </section>
    </main>
</div>
