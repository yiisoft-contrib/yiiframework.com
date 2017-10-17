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
     * @param MethodDoc|PropertyDoc|ConstDoc|EventDoc $primitive
     * @param $version
     */
    public static function createRecord($parent, $primitive, $version)
    {
        /** @var SearchApiPrimitive $model */
        $model = new static();
        $model->version = $version;
        $model->name = $primitive->name;
        $model->parentId = $parent->getPrimaryKey();
        $model->shortDescription = $primitive->shortDescription;
        $model->description = $primitive->description;
        $model->since = $primitive->since;
        $model->deprecatedSince = $primitive->deprecatedSince;
        $model->deprecatedReason = $primitive->deprecatedReason;
        $model->definedBy = $primitive->definedBy;

        if ($primitive instanceof MethodDoc) {
            $model->type = 'method';

            $model->visibility = $primitive->visibility;
            $model->isStatic = $primitive->isStatic;
            $model->isAbstract = $primitive->isAbstract;
            $model->isFinal = $primitive->isFinal;

            $params = [];
            foreach($primitive->params as $param) {
                $params[] = [
                    'name' => $param->name,
                    'description' => $param->description,
                    'isOptional' => $param->isOptional,
                    'defaultValue' => $param->defaultValue,
                    'isPassedByReference' => $param->isPassedByReference,
                    'typeHint' => $param->typeHint,
                    'types' => $param->types,
                ];
            }
            $model->params = $params;

            $exceptions = [];
            foreach($primitive->exceptions as $name => $description) {
                $exceptions[] = [
                    'name' => $name,
                    'description' => $description,
                ];
            }
            $model->exceptions = $exceptions;

            $model->return = $primitive->return;
            $model->returnTypes = $primitive->returnTypes;
            $model->isReturnByReference = $primitive->isReturnByReference;
        } elseif ($primitive instanceof PropertyDoc) {
            $model->type = 'property';

            $model->writeOnly = $primitive->isWriteOnly;
            $model->readOnly = $primitive->isReadOnly;
            $model->types = $primitive->types;
            $model->defaultValue = $primitive->defaultValue;
            $model->setter = $primitive->setter ? $primitive->setter->name : null;
            $model->getter = $primitive->getter ? $primitive->getter->name : null;

        } elseif ($primitive instanceof ConstDoc) {
            $model->type = 'const';

            $model->value = $primitive->value;

        } elseif ($primitive instanceof EventDoc) {
            $model->type = 'event';

            $model->value = $primitive->value;
        }
        $model->insert(false, null, ['op_type' => 'create', 'parent' => $model->parentId]);
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
                    // TODO improve mappings for search
                    '_parent' => ['type' => 'api-type'],
                    'properties' => [
                        'version' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'type' => ['type' => 'string', 'index' => 'not_analyzed'],

                        'name' => ['type' => 'string'],
                        'parentId' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'shortDescription' => ['type' => 'string'],
                        'description' => ['type' => 'string'],
                        'since' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'deprecatedSince' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'deprecatedReason' => ['type' => 'string'],

                        'definedBy' => ['type' => 'string', 'index' => 'not_analyzed'],

                        // methods and properties
                        'visibility' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'isStatic' => ['type' => 'boolean'],

                        // properties
                        'writeOnly' => ['type' => 'boolean'],
                        'readOnly' => ['type' => 'boolean'],
                        'types' => ['type' => 'string', 'index' => 'not_analyzed'], // array
                        'defaultValue' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'getter' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'setter' => ['type' => 'string', 'index' => 'not_analyzed'],

                        // const, event
                        'value' => ['type' => 'string', 'index' => 'not_analyzed'],

                        // method
                        'isAbstract' => ['type' => 'boolean'],
                        'isFinal' => ['type' => 'boolean'],
                        //'params', // array
                        //'exceptions', // array
                        'return' => ['type' => 'string'],
                        'returnTypes' => ['type' => 'string', 'index' => 'not_analyzed'], // array
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
        // TODO: Implement getDescription() method.
    }

    public function getType()
    {
        // TODO: Implement getTitle() method.
    }
}
