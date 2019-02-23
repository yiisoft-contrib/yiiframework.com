<?php

namespace app\widgets;

use app\models\Extension;
use app\models\ExtensionTag;
use yii\base\Widget;
use yii\helpers\Html;

class ExtensionTaglist extends Widget
{
    public $urlParams = [];

    /**
     * @var Extension
     */
    public $extension;

    public function run()
    {
        if ($this->extension) {
            $tags = $this->extension->getTags()->orderBy(['name' => SORT_ASC])->all();
        } else {
            $query = ExtensionTag::find();

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
            /** @var $tag ExtensionTag */
            $tagEntries[$tag->slug] = Html::a(Html::encode($tag->name), array_merge($this->urlParams, ['extension/index', 'tag' => $tag->slug]))
                . ($this->extension ? '' : " ($tag->frequency)");
        }

        if ($this->extension) {
            return implode(', ', $tagEntries);
        }

        return $this->render('extensionTaglist', ['tagEntries' => $tagEntries]);
    }

}
