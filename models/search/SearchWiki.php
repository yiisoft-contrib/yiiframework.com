<?php

namespace app\models\search;

use app\models\Wiki;

/**
 * Search record for Wiki
 *
 *
 * @property string $id
 * @property string $version
 * @property string $category_id
 * @property string $category
 * @property string $title
 * @property string $content
 */
class SearchWiki extends SearchActiveRecord
{
    const TYPE = 'wiki';


    public function attributes()
    {
        return [
            'id',

            'version',
            'category_id',
            'category',

            'title',
            'content',
        ];
    }

    public static function index()
    {
        return parent::index() . '-en';
    }

    /**
     * @param Wiki $wiki
     */
    public static function createRecord($wiki)
    {
        if ($wiki->status != Wiki::STATUS_PUBLISHED) {
            return;
        }
        $model = new static();
        $model->id = $wiki->id;
        $model->version = $wiki->yii_version;
        $model->category_id = $wiki->category_id;
        $model->category = $wiki->category->name;
        $model->title = $wiki->title;
        $model->content = $wiki->content;

        $model->insert(false);
    }

    /**
     * @param Wiki $wiki
     */
    public static function updateRecord($wiki)
    {
        $model = static::findOne($wiki->id);

        if ($wiki->status != Wiki::STATUS_PUBLISHED) {
            if ($model !== null) {
                $model->delete();
            }
            return;
        }

        if ($model === null) {
            $model = new static();
        }
        $model->id = $wiki->id;
        $model->version = $wiki->yii_version;
        $model->category_id = $wiki->category_id;
        $model->category = $wiki->category->name;
        $model->title = $wiki->title;
        $model->content = static::filterHtml($wiki->getContentHtml());

        $model->save(false);
    }

    /**
     * @param Wiki $wiki
     */
    public static function deleteRecord($wiki)
    {
        $model = static::findOne($wiki->id);
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
        $command->updateAnalyzers(static::index(), [
            'settings' => [
                'analysis' => [
                    'normalizer' => [
                        'lowercase' => [
                            'type' => 'custom',
                            'filter' => ['lowercase']
                        ]
                    ]
                ]
            ],
        ]);
        $mapping = $command->getMapping(static::index(), static::type());
        if (empty($mapping)) {
            $command->setMapping(static::index(), static::type(), [
                static::type() => [
                    'properties' => [
                        'version' => ['type' => 'keyword'],
                        'category_id' => ['type' => 'integer'],

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
                                // keyword field for exact case-insensitive matching
                                'keyword' => [
                                    'type' => 'keyword',
                                    'normalizer' => 'lowercase'
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
        $wiki = Wiki::findOne($this->id); // TODO eager loading, better put URL into ES
        return $wiki ? $wiki->getUrl() : null;
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
        return 'Wiki';
    }
}
