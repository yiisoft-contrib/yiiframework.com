<?php
/**
 * Created by PhpStorm.
 * User: cebe
 * Date: 09.04.15
 * Time: 19:41
 */

namespace app\models;


use yii\apidoc\models\ClassDoc;
use yii\apidoc\models\InterfaceDoc;
use yii\apidoc\models\TraitDoc;
use yii\apidoc\models\TypeDoc;

/**
 * API documentation type, i.e. class, interface or trait
 *
 *
 * @property string $version
 * @property string $language
 * @property string $title
 * @property string $body
 */
class SearchGuideSection extends SearchActiveRecord
{

    public function attributes()
    {
        return [
            'version',
            'language',

            'name',
            'title',
            'body'
        ];
    }

    public function getType()
    {
        return 'guide';
    }

    /**
     *
     */
    public static function createRecord($name, $title, $body, $version, $language)
    {
        // filter out code blocks
        $body = preg_replace('~<pre><code>.*?</code></pre>~', '', $body);
        $body = strip_tags($body);

        /** @var SearchGuideSection $model */
        $model = new static();
        $model->version = $version;
        $model->language = $language;

        $model->name = $name;
        $model->title = $title;
        $model->body = $body;

        $model->insert(false);
    }

    public static function type()
    {
        return 'guide-section';
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
                        'version' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'language' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'name' => ['type' => 'string', 'index' => 'not_analyzed'],

                        'title' => ['type' => 'string'],
                        'body' => ['type' => 'string'],
                    ]
                ]
            ]);
            $command->flushIndex(static::index());
        }
    }

    public function getUrl()
    {
        if ($this->version[0] === '1') {
            $name = $this->name;
        } else {
            $name = strtolower(str_replace('\\', '-', $this->name));
        }
        return ['guide/view', 'version' => $this->version, 'language' => $this->language, 'section' => $name];
    }
} 