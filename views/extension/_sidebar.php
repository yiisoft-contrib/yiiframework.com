<?php

use app\components\object\ClassType;
use app\models\ExtensionCategory;
use app\models\ExtensionTag;
use app\widgets\RecentComments;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $category string */
/** @var $tag \app\models\ExtensionTag */
?>
<?= Html::a('<span class="big">Create</span><span class="small">new extension</span>', ['create'], ['class' => 'btn btn-block btn-new-extension']) ?>

<?php if (isset($sort)): ?>
    <h3 class="extension-side-title">Sorting by</h3>
    <!--ul class="extension-side-menu">
        <li class="active"><a href="#">Date</a></li>
        <li><a href="#">Rating</a></li>
        <li><a href="#">Comments</a></li>
        <li><a href="#">Views</a></li>
    </ul-->

    <?= \yii\widgets\LinkSorter::widget([
        'sort' => $sort,
        'options' => [
            'class' => 'extension-side-menu sorter',
        ],
    ]) ?>

<?php endif; ?>

<h3 class="extension-side-title">Categories</h3>

<ul class="extension-side-menu">
    <li<?= empty($category) ? ' class="active"' : '' ?>><a href="<?= \yii\helpers\Url::to(['extension/index', 'tag' => isset($tag) ? $tag->slug : null])?>">All</a></li>
    <?php foreach(ExtensionCategory::findWithCountData()->all() as $cat): ?>
        <li<?= isset($category) && $category == $cat->id ? ' class="active"' : '' ?>>
            <a href="<?= \yii\helpers\Url::to([
                'extension/index',
                'category' => $cat->id,
                'tag' => isset($tag) ? $tag->slug : null,
                'version' => isset($version) ? $version : '2.0',
            ])?>"><?= Html::encode($cat->name) ?> <span class="count">(<?= (int) $cat->count ?>)</span></a>
        </li>
    <?php endforeach; ?>
</ul>

<h3 class="extension-side-title">Popular Tags</h3>

<ul class="extension-side-menu last-side-menu">
    <?php foreach(ExtensionTag::find()->orderBy(['frequency' => SORT_DESC])->limit(10)->all() as $t): ?>
        <li<?= isset($tag) && $tag->equals($t) ? ' class="active"' : '' ?>>
            <a href="<?= \yii\helpers\Url::to([
                'extension/index',
                'tag' => $t->slug,
                'category' => isset($category) ? $category : null,
                'version' => isset($version) ? $version : '2.0',
            ])?>"><?= Html::encode($t->name) ?> <span class="count">(<?= (int) $t->frequency ?>)</span></a>
        </li>
    <?php endforeach; ?>
</ul>

<?= RecentComments::widget([
    'objectType' => ClassType::COMMENT,
    'titleClass' => 'extension-side-title',
    'menuClass' => 'extension-side-comments last-side-menu',
])?>

