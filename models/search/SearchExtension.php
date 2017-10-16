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
 * @property string $name
 * @property string $title
 * @property string $content
 */
class SearchExtension extends SearchActiveRecord
{

    public function attributes()
    {
        return [
            'id',

            'name',

            'version',
            'category_id',

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
     * @param Extension $extension
     */
    public static function createRecord($extension)
    {
        $model = new static();
        $model->id = $extension->id;
        $model->name = $extension->name;
        $model->version = $extension->yii_version;
        $model->category_id = $extension->category_id;
        $model->title = $extension->tagline;
        $model->content = $extension->description;

        $model->insert(false);
    }


    /**
     * @param Extension $extension
     */
    public static function updateRecord($extension)
    {
        $model = static::findOne($extension->id);
        if ($model === null) {
            $model = new static();
        }
        $model->id = $extension->id;
        $model->name = $extension->name;
        $model->version = $extension->yii_version;
        $model->category_id = $extension->category_id;
        $model->title = $extension->tagline;
        $model->content = static::filterHtml($extension->getContentHtml());

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
        return 'extension';
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
                    // TODO improve mappings for search
                    'properties' => [
                        'version' => ['type' => 'keyword'],
                        'category_id' => ['type' => 'integer'],

                        'name' => ['type' => 'text'],
                        'title' => [
                            'type' => 'text',
                            // sub-fields added for language
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
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
        $extension = Extension::findOne($this->id); // TODO eager loading
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
