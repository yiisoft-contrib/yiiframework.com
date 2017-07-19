<?php

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
 * @property string $type class, interface, trait
 * @property string $name
 * @property string $namespace
 * @property string $shortDescription
 * @property string $description
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
            'shortDescription',
            'description',
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
        $model->name = $type->name;
        $model->namespace = $type->namespace;
        $model->shortDescription = $type->shortDescription;
        $model->description = $type->description;
        $model->since = $type->since;
        $model->deprecatedSince = $type->deprecatedSince;
        $model->deprecatedReason = $type->deprecatedReason;

        if ($type instanceof ClassDoc) {
            $model->type = 'class';
        } elseif ($type instanceof InterfaceDoc) {
            $model->type = 'interface';
        } elseif ($type instanceof TraitDoc) {
            $model->type = 'trait';
        }

        $model->insert(false);

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
        $mapping = $command->getMapping(static::index(), static::type());
        if (empty($mapping)) {
            $command->setMapping(static::index(), static::type(), [
                static::type() => [
                    // TODO improve mappings for search
                    'properties' => [
                        'version' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'type' => ['type' => 'string', 'index' => 'not_analyzed'],

                        'name' => ['type' => 'string'],
                        'namespace' => ['type' => 'string'],
                        'shortDescription' => ['type' => 'string'],
                        'description' => ['type' => 'string'],
                        'since' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'deprecatedSince' => ['type' => 'string', 'index' => 'not_analyzed'],
                        'deprecatedReason' => ['type' => 'string'],

                        // for classes
                        'extends' => ['type' => 'string'],
                        'implements' => ['type' => 'string'],
                        'traits' => ['type' => 'string'],
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
        return ['api/view', 'version' => $this->version, 'section' => $name];
    }
} 