<?php

namespace app\apidoc;

trait RendererTrait
{
    public $extensions = [
        'apidoc',
        'authclient',
        'bootstrap',
        'codeception',
        'composer',
        'debug',
        'elasticsearch',
        'faker',
        'gii',
        'imagine',
        'jui',
        'mongodb',
        'redis',
        'smarty',
        'sphinx',
        'swiftmailer',
        'symfonymailer',
        'twig',
    ];

    public function getNavTypes($type, $types)
    {
        if ($type === null) {
            return $types;
        }

        return $this->filterTypes($types, $this->getTypeCategory($type));
    }

    protected function getTypeCategory($type)
    {
        $extensions = $this->extensions;
        $navClasses = 'app';
        if (isset($type)) {
            if ($type->name == 'Yii' || $type->name == 'YiiRequirementChecker') {
                $navClasses = 'yii';
            } elseif (strncmp($type->name, 'yii\\', 4) == 0) {
                $navClasses = 'yii';
                $subName = substr($type->name, 4);
                if (($pos = strpos($subName, '\\')) !== false) {
                    $subNamespace = substr($subName, 0, $pos);
                    if (in_array($subNamespace, $extensions)) {
                        $navClasses = $subNamespace;
                    }
                }
            }
        }

        return $navClasses;
    }

    protected function filterTypes($types, $navClasses)
    {
        switch ($navClasses) {
            case 'app':
                $types = array_filter($types, function ($val) {
                    return strncmp($val->name, 'yii\\', 4) !== 0;
                });
                break;
            case 'yii':
                $self = $this;
                $types = array_filter($types, function ($val) use ($self) {
                    if ($val->name == 'Yii' || $val->name == 'YiiRequirementChecker') {
                        return true;
                    }
                    if (strlen($val->name) < 5) {
                        return false;
                    }
                    $subName = substr($val->name, 4, strpos($val->name, '\\', 5) - 4);

                    return strncmp($val->name, 'yii\\', 4) === 0 && !in_array($subName, $self->extensions);
                });
                break;
            default:
                $types = array_filter($types, function ($val) use ($navClasses) {
                    return strncmp($val->name, "yii\\$navClasses\\", strlen("yii\\$navClasses\\")) === 0;
                });
        }

        return $types;
    }
}
