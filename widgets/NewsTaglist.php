<?php

namespace app\widgets;


use app\models\News;
use app\models\NewsTag;
use Yii;
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

            if (Yii::$app->user->can('news:pAdmin')) {
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
        foreach($tags as $tag) {
            /** @var $tag NewsTag */
            $tagEntries[$tag->slug] = Html::a(Html::encode($tag->name), array_merge($this->urlParams, ['news/index', 'tag' => $tag->slug]))
                . ($this->news ? '' : " ($tag->frequency)");
        }

        return $this->render('newsTaglist', ['tagEntries' => $tagEntries]);
    }

}
