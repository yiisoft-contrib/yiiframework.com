<?php

namespace app\components\packagist;

class PackageNotFoundException extends PackagistException
{
    public function getName()
    {
        return 'Packagist Package Not Found';
    }
}
