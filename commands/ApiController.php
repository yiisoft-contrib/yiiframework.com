<?php

namespace app\commands;

use app\models\SearchApiPrimitive;
use app\models\SearchApiType;
use Yii;
use yii\apidoc\models\Context;
use yii\base\ErrorHandler;
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
            $source = [
                "$sourcePath/yii-$version/framework",
            ];
            $target = "$targetPath/api-$version";
            $cmd = Yii::getAlias("@app/data/yii-$version/build/build");

            if ($version === '1.1' && !is_file($composerYii1 = Yii::getAlias('@app/data/yii-1.1/vendor/autoload.php'))) {
                $this->stdout("WARNING: Composer dependencies of Yii 1.1 are not installed, api generation may fail.\n", Console::BOLD, Console::FG_YELLOW);
            }

            $this->stdout("Start generating API $version...\n");
            FileHelper::createDirectory($target);
            passthru("php $cmd api $target online");

            foreach(FileHelper::findFiles($target, ['only' => ['*.html']]) as $file) {
                file_put_contents($file, preg_replace(
                    '~href="/doc/api/([\w\#\-\.]*)"~i',
                    'href="' . Yii::$app->params['api.baseUrl'] . '/' . $version . '/\1"',
                    file_get_contents($file))
                );
            }
            file_put_contents("$target/api/index.html", str_replace('<h1>Class Reference</h1>', '<h1>API Documentation</h1>', file_get_contents("$target/api/index.html")));

            if (!$this->populateElasticsearch1x($source, $target)) {
                return 1;
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

    public function actionDropElasticsearchIndex()
    {
        if ($this->confirm('really drop the whole elasticsearch index? You need to rebuild it afterwards!')) {
            SearchApiType::getDb()->createCommand()->deleteIndex(SearchApiType::index());
            sleep(1);
            SearchApiType::setMappings();
            SearchApiPrimitive::setMappings();
            return 0;
        }
        return 1;
    }

    protected function populateElasticsearch1x($source, $target)
    {
        // search for files to process
        if (($files = $this->searchFiles($source)) === false) {
            return false;
        }

        // load context from cache
        $context = $this->loadContext($target);
        $this->stdout('Checking for updated files... ');
        foreach ($context->files as $file => $sha) {
            if (!file_exists($file)) {
                $this->stdout('At least one file has been removed. Rebuilding the context...');
                $context = new Context();
                if (($files = $this->searchFiles($source)) === false) {
                    return false;
                }
                break;
            }
            if (sha1_file($file) === $sha) {
                unset($files[$file]);
            }
        }
        $this->stdout('done.' . PHP_EOL, Console::FG_GREEN);

        // process files
        $fileCount = count($files);
        $this->stdout($fileCount . ' file' . ($fileCount == 1 ? '' : 's') . ' to update.' . PHP_EOL);
        Console::startProgress(0, $fileCount, 'Processing files... ', false);
        $done = 0;
        foreach ($files as $file) {
            $context->addFile($file);
            Console::updateProgress(++$done, $fileCount);
        }
        Console::endProgress(true);
        $this->stdout('done.' . PHP_EOL, Console::FG_GREEN);

        // save processed data to cache
        $this->storeContext($context, $target);

        $this->updateContext($context);

        $types = array_merge($context->classes, $context->interfaces, $context->traits);

        Console::startProgress(0, $count = count($types), 'populating elasticsearch index...', false);
        $version = $this->version;
        // first delete all records for this version
        SearchApiType::setMappings();
        SearchApiPrimitive::setMappings();
//        ApiPrimitive::deleteAllForVersion($version);
        SearchApiType::deleteAllForVersion($version);
        sleep(1);
        $i = 0;
        foreach($types as $type) {
            SearchApiType::createRecord($type, $version);
            Console::updateProgress(++$i, $count);
        }
        Console::endProgress(true, true);
        $this->stdout("done.\n", Console::FG_GREEN);

        return true;
    }
} 
