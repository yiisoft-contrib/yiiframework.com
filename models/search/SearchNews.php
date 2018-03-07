<?php

namespace app\models\search;

use app\models\News;

/**
 * Search record for News
 *
 *
 * @property string $id
 * @property string $title
 * @property string $content
 */
class SearchNews extends SearchActiveRecord
{
    const TYPE = 'news';


    public function attributes()
    {
        return [
            'id',

            'title',
            'content',
        ];
    }

    public static function primaryKey()
    {
        return ['id'];
    }

    public static function index()
    {
        return parent::index() . '-en';
    }

    /**
     *
     * @param News $news
     */
    public static function createRecord($news)
    {
        $model = new static();
        $model->id = $news->id;
        $model->title = $news->title;
        $model->content = $news->content;

        $model->insert(false);
    }

    /**
     * @param News $news
     */
    public static function updateRecord($news)
    {
        $model = static::findOne($news->id);
        if ($model === null) {
            $model = new static();
        }
        $model->id = $news->id;
        $model->title = $news->title;
        $model->content = static::filterHtml($news->getContentHtml());

        $model->save(false);
    }

    /**
     * @param News $news
     */
    public static function deleteRecord($news)
    {
        $model = static::findOne($news->id);
        if ($model !== null) {
            $model->delete();
        }
    }

    public static function type()
    {
        return self::TYPE;
    }

    public static function setMappings()
    {
        $command = static::getDb()->createCommand();
        if (!$command->indexExists(static::index())) {
            $command->createIndex(static::index());
        }
        $mapping = $command->getMapping(static::index(), static::type());
        if (empty($mapping)) {
            $command->setMapping(static::index(), static::type(), [
                static::type() => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            // sub-fields added for language
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                ],
                                // mapping for search-as-you-type completion
                                'suggest' => [
                                    'type' => 'completion',
                                ],
                            ],
                        ],
                        'content' => [
                            'type' => 'text',
                            // sub-fields added for language
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                ],
                            ],
                        ],
                    ]
                ]
            ]);
            $command->flushIndex(static::index());
        }
    }

    public function getUrl()
    {
        $news = News::findOne($this->id); // TODO eager loading, better put URL into ES
        return $news ? $news->getUrl() : null;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return '';
    }

    public function getType()
    {
        return 'News';
    }
}
