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
 * @property string $title
 * @property string $content
 */
class SearchWiki extends SearchActiveRecord
{

    public function attributes()
    {
        return [
            'id',

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
     * @param Wiki $wiki
     */
    public static function createRecord($wiki)
    {
        $model = new static();
        $model->id = $wiki->id;
        $model->version = $wiki->yii_version;
        $model->category_id = $wiki->category_id;
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
        if ($model === null) {
            $model = new static();
        }
        $model->id = $wiki->id;
        $model->version = $wiki->yii_version;
        $model->category_id = $wiki->category_id;
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
        return 'wiki';
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

                        'title' => ['type' => 'text'],
                        'content' => ['type' => 'text'],
                    ]
                ]
            ]);
            $command->flushIndex(static::index());
        }
    }

    public function getUrl()
    {
        $wiki = Wiki::findOne($this->id); // TODO eager loading
        return $wiki ? $wiki->getUrl() : null;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return 'TODO'; // TODO
    }

    public function getType()
    {
        return 'Wiki';
    }

}
