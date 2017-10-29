<?php

namespace app\components\objectKey;

use app\models\Comment;
use app\models\Extension;
use app\models\File;
use app\models\News;
use app\models\Wiki;
use yii\base\InvalidValueException;

class ObjectKeyHelper
{
    const TYPE_NEWS = 'news';
    const TYPE_WIKI = 'wiki';
    const TYPE_EXTENSION = 'extension';
    const TYPE_COMMENT = 'comment';
    const TYPE_FILE = 'file';

    const TYPE_GUIDE = 'guide';
    const TYPE_API = 'api';

    public static $typeClasses = [
        self::TYPE_NEWS => News::class,
        self::TYPE_WIKI => Wiki::class,
        self::TYPE_EXTENSION => Extension::class,
        self::TYPE_COMMENT => Comment::class,
        self::TYPE_FILE => File::class,
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
