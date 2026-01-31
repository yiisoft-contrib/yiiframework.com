<?php

namespace app\models\search;

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
    const TYPE = 'api-type';


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
        $model->type = $type['type'] ?? 'class';
        $model->name = StringHelper::basename($type['name']);
        $model->namespace = StringHelper::dirname($type['name']);
        $model->title = $type['shortDescription'] ?? null;
        $model->content = isset($type['description']) ? static::filterHtml($type['description']) : null;
        $model->since = $type['since'] ?? null;
        $model->deprecatedSince = $type['deprecatedSince'] ?? null;
        $model->deprecatedReason = $type['deprecatedReason'] ?? null;

        $model->set_id("$version/" . strtolower(ltrim(str_replace('\\', '-', "$model->namespace\\$model->name"), '-')));

        $model->insert(false, null, ['op_type' => 'index']);

        // Index methods, properties, constants and events for direct search
        if (isset($type['methods']) && is_array($type['methods'])) {
            foreach($type['methods'] as $method) {
                if (($method['visibility'] ?? 'public') === 'private') {
                    continue;
                }
                SearchApiPrimitive::createRecord($model, $method, $version);
            }
        }

        if (isset($type['properties']) && is_array($type['properties'])) {
            foreach($type['properties'] as $property) {
                if (($property['visibility'] ?? 'public') === 'private') {
                    continue;
                }
                SearchApiPrimitive::createRecord($model, $property, $version);
            }
        }

        if (isset($type['constants']) && is_array($type['constants'])) {
            foreach($type['constants'] as $const) {
                SearchApiPrimitive::createRecord($model, $const, $version);
            }
        }

        if (isset($type['events']) && is_array($type['events'])) {
            foreach($type['events'] as $event) {
                SearchApiPrimitive::createRecord($model, $event, $version);
            }
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
                                // mapping for search-as-you-type completion
                                'suggest' => [
                                    'type' => 'completion',
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
            $name = strtolower(str_replace('\\', '-', ltrim("$this->namespace\\$this->name", '\\')));
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
