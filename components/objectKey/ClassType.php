<?php

namespace app\components\objectKey;

use app\models\Comment;
use app\models\Extension;
use app\models\File;
use app\models\News;
use app\models\Wiki;
use yii\base\InvalidValueException;

class ClassType
{
    const NEWS = 'news';
    const WIKI = 'wiki';
    const EXTENSION = 'extension';
    const COMMENT = 'comment';
    const FILE = 'file';

    const GUIDE = 'guide';
    const API = 'api';

    public static $typeClasses = [
        self::NEWS => News::class,
        self::WIKI => Wiki::class,
        self::EXTENSION => Extension::class,
        self::COMMENT => Comment::class,
        self::FILE => File::class,
    ];

    /**
     * @param string $type
     *
     * @return string
     */
    public static function getClass($type)
    {
        if (array_key_exists($type, static::$typeClasses)) {
            return static::$typeClasses[$type];
        }

        throw new InvalidValueException("Type is '{$type}' not found.");
    }
}
