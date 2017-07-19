<?php
/**
 * Created by PhpStorm.
 * User: cebe
 * Date: 18.05.17
 * Time: 21:17
 */

namespace app\components\packagist;


use yii\base\Exception;

class PackageNotFoundException extends Exception
{
    public function getName()
    {
        return 'Packagist Package Not Found';
    }
}
