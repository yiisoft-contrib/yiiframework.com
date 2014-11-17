<?php

namespace app\apidoc;

class ApiController extends \yii\apidoc\commands\ApiController
{
    protected function findRenderer($template)
    {
        return new ApiRenderer;
    }
} 
