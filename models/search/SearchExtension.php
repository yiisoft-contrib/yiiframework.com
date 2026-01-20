<?php

namespace app\models\search;

use app\models\Extension;

/**
 * Search record for Extensions
 *
 *
 * @property string $id
 * @property string $version
 * @property string $category_id
 * @property string $category
 * @property string $name
 * @property string $title
 * @property string $content
 */
class SearchExtension extends SearchActiveRecord
{
    const TYPE = 'extension';


    public function attributes()
    {
        return [
            'id',

            'name',

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
     *
     * @param Extension $extension
     */
    public static function createRecord($extension)
    {
        if ($extension->status != Extension::STATUS_PUBLISHED) {
            return;
        }

        $model = new static();
        $model->id = $extension->id;
        $model->name = $extension->name;
        $model->version = $extension->yii_version;
        $model->category_id = $extension->category_id;
        $model->category = $extension->category->name;
        $model->content = $extension->description;

        $model->title = trim($extension->tagline);
        if (empty($model->title)) {
            $model->title = $extension->name;
        }

        $model->insert(false);
    }


    /**
     * @param Extension $extension
     */
    public static function updateRecord($extension)
    {
        $model = static::findOne($extension->id);

        if ($extension->status != Extension::STATUS_PUBLISHED) {
            if ($model !== null) {
                $model->delete();
            }
            return;
        }

        if ($model === null) {
            $model = new static();
        }
        $model->id = $extension->id;
        $model->name = $extension->name;
        $model->version = $extension->yii_version;
        $model->category_id = $extension->category_id;
        $model->category = $extension->category->name;
        $model->content = static::filterHtml($extension->getContentHtml());

        $model->title = trim($extension->tagline);
        if (empty($model->title)) {
            $model->title = $extension->name;
        }

        $model->save(false);
    }

    /**
     * @param Extension $extension
     */
    public static function deleteRecord($extension)
    {
        $model = static::findOne($extension->id);
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

                        'name' => [
                            'type' => 'text',
                            'fields' => [
                                // keyword field for exact case-insensitive matching
                                'keyword' => [
                                    'type' => 'keyword',
                                    'normalizer' => 'lowercase'
                                ],
                            ],
                        ],
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
        $extension = Extension::findOne($this->id); // TODO eager loading, better put URL into ES
        return $extension ? $extension->getUrl() : null;
    }

    public function getTitle()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->title;
    }

    public function getType()
    {
        return 'Extension';
    }
}
