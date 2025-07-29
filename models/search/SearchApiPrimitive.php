<?php

namespace app\models\search;

use yii\apidoc\models\ConstDoc;
use yii\apidoc\models\EventDoc;
use yii\apidoc\models\MethodDoc;
use yii\apidoc\models\PropertyDoc;

/**
 * API documentation primitive, i.e. method, property, const, event
 *
 * @property string $version
 * @property string $type method, property, const, event
 * @property string $name
 * @property string $parentId
 * @property string $shortDescription
 * @property string $description
 * @property string $since
 * @property string $deprecatedSince
 * @property string $deprecatedReason
 * @property string $visibility
 * @property boolean $isStatic
 * @property array $types
 * @property string $definedBy
 * @property boolean $writeOnly
 * @property boolean $readOnly
 * @property string $defaultValue
 * @property string $getter
 * @property string $setter
 * @property mixed $value
 * @property boolean $isAbstract
 * @property boolean $isFinal
 * @property array $params
 * @property array $exceptions
 * @property string $return
 * @property string $returnTypes
 * @property boolean $isReturnByReference
 */
class SearchApiPrimitive extends SearchActiveRecord
{
    const TYPE = 'api-primitive';


    public function attributes()
    {
        return [
            'version',
            'type',

            'name',
            'parentId',
            'shortDescription',
            'description',
            'since',
            'deprecatedSince',
            'deprecatedReason',

            'definedBy',

            // methods and properties
            'visibility',
            'isStatic',

            // properties
            'writeOnly',
            'readOnly',
            'types', // array
            'defaultValue',
            'getter',
            'setter',

            // const, event
            'value',

            // method
            'isAbstract',
            'isFinal',
            'params', // array
            'exceptions', // array
            'return',
            'returnTypes', // array
            'isReturnByReference',
        ];
    }

    public static function index()
    {
        return parent::index() . '-en';
    }

    /**
     *
     * @param SearchApiType $parent
     * @param array $primitive array data from API documentation
     * @param $version
     */
    public static function createRecord($parent, $primitive, $version)
    {
        /** @var SearchApiPrimitive $model */
        $model = new static();
        $model->version = $version;
        $model->name = $primitive['name'] ?? '';
        $model->parentId = $parent->get_id();
        $model->shortDescription = $primitive['shortDescription'] ?? '';
        $model->description = isset($primitive['description']) ? static::filterHtml($primitive['description']) : '';
        $model->since = $primitive['since'] ?? null;
        $model->deprecatedSince = $primitive['deprecatedSince'] ?? null;
        $model->deprecatedReason = $primitive['deprecatedReason'] ?? null;
        $model->definedBy = $primitive['definedBy'] ?? ($parent->namespace ? $parent->namespace . '\\' . $parent->name : $parent->name);

        // Determine type from the primitive data structure
        if (isset($primitive['params']) || isset($primitive['return'])) {
            $model->type = 'method';
            
            $model->visibility = $primitive['visibility'] ?? 'public';
            $model->isStatic = $primitive['isStatic'] ?? false;
            $model->isAbstract = $primitive['isAbstract'] ?? false;
            $model->isFinal = $primitive['isFinal'] ?? false;

            $model->params = $primitive['params'] ?? [];
            $model->exceptions = $primitive['exceptions'] ?? [];
            $model->return = $primitive['return'] ?? null;
            $model->returnTypes = $primitive['returnTypes'] ?? [];
            $model->isReturnByReference = $primitive['isReturnByReference'] ?? false;
            
        } elseif (isset($primitive['types']) || isset($primitive['defaultValue'])) {
            $model->type = 'property';

            $model->visibility = $primitive['visibility'] ?? 'public';
            $model->isStatic = $primitive['isStatic'] ?? false;
            $model->writeOnly = $primitive['isWriteOnly'] ?? false;
            $model->readOnly = $primitive['isReadOnly'] ?? false;
            $model->types = $primitive['types'] ?? [];
            $model->defaultValue = $primitive['defaultValue'] ?? null;
            $model->setter = $primitive['setter'] ?? null;
            $model->getter = $primitive['getter'] ?? null;

        } elseif (isset($primitive['value'])) {
            // Check if this is an event or constant
            if (isset($primitive['trigger']) || strpos($model->name, 'EVENT_') === 0) {
                $model->type = 'event';
            } else {
                $model->type = 'const';
            }
            $model->value = $primitive['value'];
        } else {
            // Default to const if we can't determine type
            $model->type = 'const';
            $model->value = $primitive['value'] ?? null;
        }
        
        $model->set_id($parent->get_id() . '::' . $model->name);
        $model->insert(false, null, ['op_type' => 'index']);
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
                        'version' => ['type' => 'keyword'],
                        'type' => ['type' => 'keyword'],

                        'name' => [
                            'type' => 'text',
                            'fields' => [
                                'suggest' => [
                                    'type' => 'completion',
                                ],
                            ],
                        ],
                        'parentId' => ['type' => 'keyword'],
                        'shortDescription' => ['type' => 'text'],
                        'description' => [
                            'type' => 'text',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                ],
                            ],
                        ],
                        'since' => ['type' => 'keyword'],
                        'deprecatedSince' => ['type' => 'keyword'],
                        'deprecatedReason' => ['type' => 'text'],

                        'definedBy' => ['type' => 'keyword'],

                        // methods and properties
                        'visibility' => ['type' => 'keyword'],
                        'isStatic' => ['type' => 'boolean'],

                        // properties
                        'writeOnly' => ['type' => 'boolean'],
                        'readOnly' => ['type' => 'boolean'],
                        'types' => ['type' => 'keyword'], // array
                        'defaultValue' => ['type' => 'keyword'],
                        'getter' => ['type' => 'keyword'],
                        'setter' => ['type' => 'keyword'],

                        // const, event
                        'value' => ['type' => 'keyword'],

                        // method
                        'isAbstract' => ['type' => 'boolean'],
                        'isFinal' => ['type' => 'boolean'],
                        'return' => ['type' => 'text'],
                        'returnTypes' => ['type' => 'keyword'], // array
                        'isReturnByReference' => ['type' => 'boolean'],

                    ],
                ],
            ]);
            $command->flushIndex(static::index());
        }
    }

    public function getUrl()
    {
        $parent = strtolower(str_replace('\\', '-', $this->definedBy));
        $name = $this->name . ($this->type === 'method' ? '()' : '') . '-detail';
        return ['api/view', 'version' => $this->version, 'section' => $parent, '#' => $name];
    }

    public function getTitle()
    {
        return $this->definedBy . '::' . $this->name;
    }

    public function getDescription()
    {
        return $this->shortDescription ?: $this->description;
    }

    public function getType()
    {
        return $this->getAttribute('type');
    }
}
