<?php

namespace app\models\search;


use yii\apidoc\models\ClassDoc;
use yii\apidoc\models\InterfaceDoc;
use yii\apidoc\models\TraitDoc;
use yii\apidoc\models\TypeDoc;
use yii\helpers\StringHelper;

/**
 * API documentation type, i.e. class, interface or trait
 *
 *
 * @property string $version
 * @property string $type class, interface, trait
 * @property string $name
 * @property string $namespace
 * @property string $title
 * @property string $content
 * @property string $since
 * @property string $deprecatedSince
 * @property string $deprecatedReason
 * @property array $extends
 * @property array $implements
 * @property array $traits
 */
class SearchApiType extends SearchActiveRecord
{

    public function attributes()
    {
        return [
            'version',
            'type',

            'name',
            'namespace',
            'title',
            'content',
            'since',
            'deprecatedSince',
            'deprecatedReason',

            // for classes
            'extends',
            'implements',
            'traits',
        ];
    }

    public static function index()
    {
        return parent::index() . '-en';
    }

    /**
     *
     * @param TypeDoc $type
     * @param $version
     */
    public static function createRecord($type, $version)
    {
        /** @var SearchApiType $model */
        $model = new static();
        $model->version = $version;
        $model->type = $type['type'];
        $model->name = StringHelper::basename($type['name']);
        $model->namespace = StringHelper::dirname($type['name']);
        $model->title = $type['shortDescription'];
        $model->content = static::filterHtml($type['description']);
//        $model->description = $type['name']; // TODO
//        $model->since = $type->since;
//        $model->deprecatedSince = $type->deprecatedSince;
//        $model->deprecatedReason = $type->deprecatedReason;

        $model->primaryKey = "$version/" . strtolower(ltrim(str_replace('\\', '-', "$model->namespace\\$model->name"), '-'));

        $model->insert(false, null, ['op_type' => 'index']);

        if ($type->methods !== null) {
            foreach($type->methods as $method) {
                if ($method->visibility === 'private') {
                    continue;
                }
                SearchApiPrimitive::createRecord($model, $method, $version);
            }
        }

        if ($type->properties !== null) {
            foreach($type->properties as $property) {
                if ($property->visibility === 'private') {
                    continue;
                }
                SearchApiPrimitive::createRecord($model, $property, $version);
            }
        }

        if ($type instanceof ClassDoc) {
            foreach($type->constants as $const) {
                SearchApiPrimitive::createRecord($model, $const, $version);
            }

            foreach($type->events as $event) {
                SearchApiPrimitive::createRecord($model, $event, $version);
            }
        }
    }

    public static function type()
    {
        return 'api-type';
    }

    public static function setMappings()
    {
        $command = static::getDb()->createCommand();
        if (!$command->indexExists(static::index())) {
            $command->createIndex(static::index());
        }
        $command->updateAnalyzers(static::index(), [
            'settings' => [
                // create a camelcase analyzer
                // https://www.elastic.co/guide/en/elasticsearch/reference/5.6/analysis-pattern-analyzer.html#_camelcase_tokenizer
                'analysis' => [
                    'analyzer' => [
                        'camel' => [
                            'type' => 'pattern',
                            'pattern' => '([^\\p{L}\\d]+)|(?<=\\D)(?=\\d)|(?<=\\d)(?=\\D)|(?<=[\\p{L}&&[^\\p{Lu}]])(?=\\p{Lu})|(?<=\\p{Lu})(?=\\p{Lu}[\\p{L}&&[^\\p{Lu}]])',
                        ]
                    ]
                ]
            ],
        ]);
        $mapping = $command->getMapping(static::index(), static::type());
        if (empty($mapping)) {
            $command->setMapping(static::index(), static::type(), [
                static::type() => [
                    // TODO improve mappings for search
                    'properties' => [
                        'version' => ['type' => 'keyword'],
                        'type' => ['type' => 'keyword'],

                        'name' => [
                            'type' => 'text',
                            'fields' => [
                                'camel' => [
                                    'type' => 'text',
                                    'analyzer' => 'camel',
                                ],
                            ],
                        ],
                        'namespace' => ['type' => 'keyword'],

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
                        'since' => ['type' => 'keyword'],
                        'deprecatedSince' => ['type' => 'keyword'],
                        'deprecatedReason' => ['type' => 'keyword'],

                        // for classes
                        'extends' => ['type' => 'keyword'],
                        'implements' => ['type' => 'keyword'],
                        'traits' => ['type' => 'keyword'],
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
            $name = strtolower(str_replace('\\', '-', "$this->namespace\\$this->name"));
        }
        return ['api/view', 'version' => $this->version, 'section' => $name];
    }

    public function getTitle()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->getAttribute('title');
    }

    public function getType()
    {
        return $this->getAttribute('type');
    }
}