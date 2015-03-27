<?php

namespace app\commands;

use Yii;
use yii\helpers\Console;
use app\apidoc\ApiRenderer;
use yii\helpers\FileHelper;

/**
 * Generates API documentation for Yii.
 */
class ApiController extends \yii\apidoc\commands\ApiController
{
    public $defaultAction = 'generate';
    public $guidePrefix = '';
    protected $version = '2.0';

    /**
     * Generates the API documentation for the specified version of Yii.
     * @param string $version version number, such as 1.1, 2.0
     * @return integer exit status
     */
    public function actionGenerate($version)
    {
        $versions = Yii::$app->params['api.versions'];
        if (!in_array($version, $versions)) {
            $this->stderr("Unknown version $version. Valid versions are " . implode(', ', $versions) . "\n\n", Console::FG_RED);
            return 1;
        }
        $this->version = $version;

        $targetPath = Yii::getAlias('@app/data');
        $sourcePath = Yii::getAlias('@app/data');

        if ($version[0] === '2') {
            $source = [
                "$sourcePath/yii-$version/framework",
//                "$sourcePath/yii-$version/extensions",
            ];
            $target = "$targetPath/api-$version";
            $this->guide = Yii::$app->params['guide.baseUrl'] . "/{$this->version}/en";

            $this->stdout("Start generating API $version...\n");
            $this->actionIndex($source, $target);
            $this->stdout("Finished API $version.\n\n", Console::FG_GREEN);
        } elseif ($version[0] === '1') {
            $target = "$targetPath/api-$version";
            $cmd = Yii::getAlias('@app/data/yii-1.1/build/build');

            if (!is_file($composerYii1 = Yii::getAlias('@app/data/yii-1.1/vendor/autoload.php'))) {
                $this->stdout("WARNING: Composer dependencies of Yii 1.1 are not installed, api generation may fail.\n", Console::BOLD, Console::FG_YELLOW);
            }

            $this->stdout("Start generating API $version...\n");
            FileHelper::createDirectory($target);
            passthru("php $cmd api $target online");

            foreach(FileHelper::findFiles($target, ['only' => ['*.html']]) as $file) {
                file_put_contents($file, preg_replace(
                    '~href="/doc/api/(\w+)"~',
                    'href="' . Yii::$app->params['api.baseUrl'] . '/' . $version . '/\1"',
                    file_get_contents($file))
                );
            }

            $this->stdout("Finished API $version.\n\n", Console::FG_GREEN);
        }

        return 0;
    }

    protected function findRenderer($template)
    {
        return new ApiRenderer([
            'version' => $this->version,
        ]);
    }
} 
