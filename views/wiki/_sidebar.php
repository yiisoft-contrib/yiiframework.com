<?php

use yii\helpers\Html;

?>
<?= Html::a('<span class="big">Write</span><span class="small">new article</span>', ['create'], ['class' => 'btn btn-block btn-new-wiki-article']) ?>
<input type="text" class="form-control wiki-search" id="search" name="q" placeholder="Search wiki…" autocomplete="off" value="">

<?php if ($this->context->action->id === 'index'): ?>
    <h3 class="wiki-side-title">Sorting by</h3>
    <ul class="wiki-side-menu">
        <li class="active"><a href="#">Date</a></li>
        <li><a href="#">Rating</a></li>
        <li><a href="#">Comments</a></li>
        <li><a href="#">Views</a></li>
    </ul>
<?php endif; ?>

<h3 class="wiki-side-title">Categories</h3>
<ul class="wiki-side-menu">
    <li class="active"><a href="#">All</a></li>
    <li><a href="#">Tips <span class="count">(168)</span></a></li>
    <li><a href="#">How-tos <span class="count">(367)</span></a></li>
    <li><a href="#">Tutorials <span class="count">(266)</span></a></li>
    <li><a href="#">FAQs <span class="count">(9)</span></a></li>
    <li><a href="#">Others <span class="count">(26)</span></a></li>
</ul>

<h3 class="wiki-side-title">Popular Tags</h3>
<ul class="wiki-side-menu last-side-menu">
    <li><a href="#">Ajax <span class="count">(52)</span></a></li>
    <li><a href="#">Authentification <span class="count">(23)</span></a></li>
</ul>
