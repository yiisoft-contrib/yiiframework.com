<?php

namespace app\components\packagist;

use yii\base\Exception;

class PackagistException extends Exception
{
    public function getName()
    {
        return 'Packagist Exception';
    }
}
