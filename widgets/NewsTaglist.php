<?php

namespace app\widgets;


use app\components\UserPermissions;
use app\models\News;
use app\models\NewsTag;
use yii\base\Widget;
use yii\helpers\Html;

class NewsTaglist extends Widget
{
    public $urlParams = [];

    /**
     * @var News
     */
    public $news;

    public function run()
    {
        if ($this->news) {
            $tags = $this->news->getTags()->orderBy(['name' => SORT_ASC])->all();
        } else {
            $query = NewsTag::find();

            if (UserPermissions::canManageNews()) {
                $query->where('frequency > 1')
                      ->orderBy(['frequency' => SORT_DESC]);
            } else {
                $query->select(['id' => 'news_tag_id', 'name', 'news_tags.slug', 'frequency' => 'COUNT(*)'])
                    ->joinWith(['news'])
                      ->andWhere(['news.status' => News::STATUS_PUBLISHED])
                      ->groupBy(['news_tag_id', 'name', 'slug'])
                      ->orderBy(['frequency' => SORT_DESC]);
            }

            $tags = $query->limit(10)->all();
        }

        $tagEntries = [];
        $urlParams = $this->urlParams;
        unset($urlParams['year']);
        foreach($tags as $tag) {
            /** @var $tag NewsTag */
            $label = Html::encode($tag->name);
            if (!$this->news) {
                $label .= ' <span class="count">' . (int) $tag->frequency . '</span>';
            }
            $tagEntries[$tag->slug] = Html::a($label, array_merge($urlParams, ['news/index', 'tag' => $tag->slug]));
        }

        return $this->render('newsTaglist', ['tagEntries' => $tagEntries]);
    }

}
