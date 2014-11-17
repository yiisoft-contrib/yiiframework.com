<?php

namespace app\apidoc;

class GuideController extends \yii\apidoc\commands\GuideController
{
    protected function findRenderer($template)
    {
        return new GuideRenderer;
    }
}
