<?php
use app\components\object\ClassType;
use app\models\WikiCategory;
use app\widgets\RecentComments;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $category string */
/** @var $version string */
/** @var $tag \app\models\WikiTag */
/** @var $popularTags \app\models\WikiTag[] */
?>
<?= Html::a('<span class="big">Write</span><span class="small">new article</span>', ['create'], ['class' => 'btn btn-block btn-new-wiki-article']) ?>

<?php if (isset($sort)): ?>
    <h3 class="wiki-side-title">Sorting by</h3>
    <!--ul class="wiki-side-menu">
        <li class="active"><a href="#">Date</a></li>
        <li><a href="#">Rating</a></li>
        <li><a href="#">Comments</a></li>
        <li><a href="#">Views</a></li>
    </ul-->

    <?= \yii\widgets\LinkSorter::widget([
        'sort' => $sort,
        'options' => [
            'class' => 'wiki-side-menu sorter',
        ],
    ]) ?>

<?php endif; ?>

<h3 class="wiki-side-title">Categories</h3>

<ul class="wiki-side-menu">
    <li<?= empty($category) ? ' class="active"' : '' ?>><a href="<?= Url::to(['wiki/index', 'tag' => isset($tag) ? $tag->slug : null])?>">All</a></li>
    <?php foreach(WikiCategory::findWithCountData()->all() as $cat): ?>
        <li<?= isset($category) && $category == $cat->id ? ' class="active"' : '' ?>>
            <a href="<?= Url::to([
                'wiki/index',
                'category' => $cat->id,
                'tag' => isset($tag) ? $tag->slug : null,
                'version' => isset($version) ? $version : '2.0',
            ])?>"><?= Html::encode($cat->name) ?> <span class="count"><?= (int) $cat->count ?></span></a>
        </li>
    <?php endforeach; ?>
</ul>

<h3 class="wiki-side-title">Popular Tags</h3>

<ul class="wiki-side-menu">
    <li<?= empty($tag) ? ' class="active"' : '' ?>><a href="<?= Url::to(['wiki/index', 'category' => isset($category) ? $category : null])?>">All</a></li>
    <?php foreach($popularTags as $t): ?>
        <li<?= isset($tag) && $tag->equals($t) ? ' class="active"' : '' ?>>
            <a href="<?= Url::to([
                'wiki/index',
                'tag' => $t->slug,
                'category' => isset($category) ? $category : null,
                'version' => isset($version) ? $version : '2.0',
            ])?>"><?= Html::encode($t->name) ?> <span class="count"><?= (int) $t->frequency ?></span></a>
        </li>
    <?php endforeach; ?>
</ul>

<?= RecentComments::widget([
    'objectType' => ClassType::WIKI,
    'titleClass' => 'wiki-side-title',
    'menuClass' => 'wiki-side-comments last-side-menu',
])?>
