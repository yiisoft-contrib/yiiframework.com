<?php
// NOTE: the books section should ideally be a picture that flips when the user hovers over it,
// revealing a description of what the Books for Yii is about and how it helps both the project
// and the user thinking about buying them.
// Tech could be Zurb's sass motion UI library for animations - that is: css3.
// See http://zurb.com/playground/motion-ui
use yii\helpers\Url;
?>
<!-- start of bookscommunity -->
<section class="section-bookscommunity">
   <div class="container">
    <div class="row">
        <div class="col-md-3">
            <h2>Books</h2>
            <div class="thumb">
                <div class="overlay"></div>
                <div class="more-link">
                    <a href="<?= Url::to(['books']) ?>">Books about Yii</a>
                </div>
                <a href="<?= Url::to(['books']) ?>" class="thumbnail"><img src="<?= Yii::getAlias('@web/image/books/yii2-testing.png') ?>" width=100 title="Books" alt="Books"/></a>
            </div>
        </div>
        <div class="col-md-9">
            <h2>Community activity</h2>
            <p>
                This could be latest forum activity, project repository feed, whatever..
            </p>
        </div>
    </div>
   </div>
</section>
<!-- end of bookscommunity -->
