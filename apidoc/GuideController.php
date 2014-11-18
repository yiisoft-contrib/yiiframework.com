<?php

namespace app\apidoc;

use Yii;
use yii\helpers\Console;

class GuideController extends \yii\apidoc\commands\GuideController
{
    public $defaultAction = 'all';
    public $guidePrefix = '';

    public function actionAll()
    {
        $targetPath = Yii::getAlias('@app/data');
        $sourcePath = Yii::getAlias('@app/data');

        $versions = Yii::$app->params['guide.versions'];
        foreach ($versions as $version => $languages) {
            if ($version[0] === '2') {
                foreach ($languages as $language => $name) {
                    $source = "$sourcePath/yii-$version/docs/guide";
                    if ($language !== 'en') {
                        $source .= "-$language";
                    }
                    $target = "$targetPath/guide-$version/$language";

                    $this->stdout("Start generating guide $version in $name...\n");
                    $this->actionIndex([$source], $target);
                    $this->stdout("Finished guide $version in $name.\n\n", Console::FG_GREEN);
                }
            }
        }
    }

    protected function findRenderer($template)
    {
        return new GuideRenderer;
    }
}
