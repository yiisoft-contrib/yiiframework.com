<?php

namespace app\apidoc;

use SplObjectStorage;
use yii\apidoc\models\ClassDoc;
use yii\apidoc\models\Context;
use yii\apidoc\models\InterfaceDoc;

/**
 * Builds the API context needed for Markdown links without full documentation or source code.
 */
final class MarkdownContext
{
    public static function create(Context $context): Context
    {
        $result = new Context();
        $subjects = new SplObjectStorage();

        foreach (['classes', 'interfaces', 'traits'] as $category) {
            foreach ($context->$category as $name => $type) {
                $class = get_class($type);
                $copy = new $class();
                $copy->name = $type->name;
                $copy->namespace = $type->namespace;
                $copy->phpDocContext = $type->phpDocContext;
                $copy->sourceFile = $type->sourceFile;

                if ($type instanceof ClassDoc) {
                    $copy->parentClass = $type->parentClass;
                    $copy->interfaces = $type->interfaces;
                    $copy->traits = $type->traits;
                } elseif ($type instanceof InterfaceDoc) {
                    $copy->parentInterfaces = $type->parentInterfaces;
                }

                foreach (['methods', 'properties', 'constants', 'events'] as $members) {
                    if (!property_exists($type, $members)) {
                        continue;
                    }
                    foreach ($type->$members as $memberName => $member) {
                        // Inherited members are shared by many types. Keep that sharing in the cache.
                        if (!isset($subjects[$member])) {
                            $memberClass = get_class($member);
                            $subject = new $memberClass();
                            $subject->name = $member->name;
                            $subject->definedBy = $member->definedBy;
                            $subjects[$member] = $subject;
                        }
                        $copy->{$members}[$memberName] = $subjects[$member];
                    }
                }

                $result->{$category}[$name] = $copy;
            }
        }

        return $result;
    }
}
