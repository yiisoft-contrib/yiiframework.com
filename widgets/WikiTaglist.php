<?php

namespace app\widgets;


use app\models\News;
use app\models\NewsTag;
use app\models\Wiki;
use app\models\WikiTag;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class WikiTaglist extends Widget
{
    public $urlParams = [];

    /**
     * @var Wiki
     */
    public $wiki;

    public function run()
    {
        if ($this->wiki) {
            $tags = $this->wiki->getTags()->orderBy(['name' => SORT_ASC])->all();
        } else {
            $query = WikiTag::find();

//            if (Yii::$app->user->can('wiki:pAdmin')) {
                $query->where('frequency > 1')
                      ->orderBy(['frequency' => SORT_DESC]);
//            } else {
//                $query->select(['id' => 'news_tag_id', 'name', 'news_tags.slug', 'frequency' => 'COUNT(*)'])
//                    ->joinWith(['news'])
//                      ->andWhere(['news.status' => News::STATUS_PUBLISHED])
//                      ->groupBy(['news_tag_id', 'name', 'slug'])
//                      ->orderBy(['frequency' => SORT_DESC]);
//            }

            $tags = $query->limit(10)->all();
        }

        $tagEntries = [];
        foreach($tags as $tag) {
            /** @var $tag NewsTag */
            $tagEntries[$tag->slug] = Html::a(Html::encode($tag->name), array_merge($this->urlParams, ['wiki/index', 'tag' => $tag->slug]))
                . ($this->wiki ? '' : " ($tag->frequency)");
        }

        if ($this->wiki) {
            return implode(', ', $tagEntries);
        } else {
            return $this->render('wikiTaglist', ['tagEntries' => $tagEntries]);
        }
    }

}
