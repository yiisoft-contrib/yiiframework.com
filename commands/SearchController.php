<?php

namespace app\commands;


use app\models\ActiveRecord;
use app\models\Extension;
use app\models\News;
use app\models\search\SearchActiveRecord;
use app\models\search\SearchApiType;
use app\models\search\SearchExtension;
use app\models\search\SearchGuideSection;
use app\models\search\SearchNews;
use app\models\search\SearchWiki;
use app\models\Wiki;
use yii\console\Controller;
use Yii;
use yii\elasticsearch\Connection;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\StringHelper;

class SearchController  extends Controller
{
    /**
     * @var bool whether to show a progress bar.
     */
    public $progress = false;


    public function options($actionID)
    {
        if ($actionID === 'ranking') {
            return array_merge(parent::options($actionID), ['progress']);
        }
        return parent::options($actionID);
    }

    public function actionIndex()
    {
        $this->stdout("Search settings\n\n", Console::BOLD);

        /** @var $es Connection */
        $es = Yii::$app->elasticsearch;

        $this->stdout('Elasticsearch config: ' . print_r($es->nodes, true));

        $this->stdout('Elasticsearch indices: ' . print_r(array_keys($es->createCommand()->getIndexStats()['indices']), true));


    }

    /**
     * Test how good common search queries work.
     */
    public function actionTest()
    {
        $queries = [
            'Install Yii',
            'How the hell do I isntall Yii?', // typo intentionally added
            'Active Record',
            'ActiveRecord',
            'Dataprovider',
            'logging',
            'logs',
            'asset management',
            'theme yii',
            'how to get started?',
            'grid view',
            'gridview',
            'cgridview',
        ];

        foreach ($queries as $query) {
            $this->stdout("$query\n", Console::BOLD);
            $q = SearchActiveRecord::search($query);
            foreach($q->limit(5)->all() as $item) {
                $this->stdout(" - ");
                $this->stdout("$item->type ", Console::FG_YELLOW);
                if ($item->hasAttribute('version')) {
                    $this->stdout("$item->version ", Console::FG_BLUE);
                }
                if ($item->hasAttribute('language')) {
                    $this->stdout("$item->language ", Console::FG_GREEN);
                }
                $this->stdout($item->getTitle() . "\n");
            }
        }
    }

    /**
     * Drop all search indices
     */
    public function actionClear()
    {
        // TODO set index analyzer:
        // https://www.elastic.co/guide/en/elasticsearch/reference/5.6/indices-update-settings.html#update-settings-analysis
        // https://www.elastic.co/guide/en/elasticsearch/reference/5.6/indices-analyze.html

        $index = SearchActiveRecord::index();
        $command = SearchActiveRecord::getDb()->createCommand();
        if ($command->indexExists($index)) {
            $command->deleteIndex($index);
        }
        foreach (SearchActiveRecord::$languages as $lang => $analyzer) {
            if ($command->indexExists("$index-$lang")) {
                $command->deleteIndex("$index-$lang");
            }
        }
    }

    /**
     * Rebuild search index.
     */
    public function actionRebuild(array $items = ['guide', 'api', 'extension', 'wiki', 'news'])
    {
        foreach($items as $item) {
            switch($item) {
                case 'guide':
                    // SearchGuideSection::deleteAll();
                    SearchGuideSection::setMappings();
                    $this->rebuildGuideIndex();
                    break;
                case 'api':
                    // SearchGuideSection::deleteAll();
                    SearchApiType::setMappings();
                    $this->rebuildApiIndex();
                    break;
                case 'extension':
                    SearchExtension::deleteAll();
                    SearchExtension::setMappings();
                    $this->rebuildIndexFor(Extension::class, SearchExtension::class);
                    break;
                case 'wiki':
                    SearchWiki::deleteAll();
                    SearchWiki::setMappings();
                    $this->rebuildIndexFor(Wiki::class, SearchWiki::class);
                    break;
                case 'news':
                    SearchNews::deleteAll();
                    SearchNews::setMappings();
                    $this->rebuildIndexFor(News::class, SearchNews::class);
                    break;
                default:
                    $this->stdout("Unknown index type: $item\n", Console::BOLD, Console::FG_RED);
                    break;
            }
        }
    }

    /**
     * @param ActiveRecord $modelClass
     * @param SearchActiveRecord $searchClass
     */
    private function rebuildIndexFor($modelClass, $searchClass)
    {
        $count = $modelClass::find()->count();
        Console::startProgress(0, $count, 'Reindexing ' . Inflector::pluralize(StringHelper::basename($modelClass)) . ' ');
        $i = 0;
        foreach($modelClass::find()->each() as $model) {
            $searchClass::updateRecord($model);
            Console::updateProgress(++$i, $count);
        }
        Console::endProgress();
    }

    private function rebuildGuideIndex()
    {
        $versions = Yii::$app->params['guide.versions'];
        foreach($versions as $version => $languages) {

            $targetPath = Yii::getAlias('@app/data');
            $sourcePath = Yii::getAlias('@app/data');

            if ($version[0] === '2') {
                foreach ($languages as $language => $name) {
                    $this->stdout("Reindexing guide $version $language ...");
                    $source = GuideController::normalizeGuideDirectory("$sourcePath/yii-$version/docs/guide", $language);;
                    $target = "$targetPath/guide-$version/$language";
                    $this->generateIndex($source, $target, $version, $language);
                    $this->stdout("done.\n", Console::BOLD, Console::FG_GREEN);
                }
            }

            if ($version[0] === '1') {
                foreach ($languages as $language => $name) {
                    $unnormalizedLanguage = strtolower(str_replace('-', '_', $language));

                    $this->stdout("Reindexing guide $version $language ...");

                    $source = "$sourcePath/yii-$version/docs/guide";
                    $target = "$targetPath/guide-$version/$language";
                    $this->generateIndexYii1($source, $target, $version, $unnormalizedLanguage);

                    $this->stdout("done.\n", Console::BOLD, Console::FG_GREEN);
                }

                // generate blog tutorial
                if (isset(Yii::$app->params['blogtut.versions'][$version])) {
                    foreach(Yii::$app->params['blogtut.versions'][$version] as $language => $name) {
                        $unnormalizedLanguage = strtolower(str_replace('-', '_', $language));

                        $this->stdout("Reindexing blogtut $version $language ...");

                        $source = "$sourcePath/yii-$version/docs/blog";
                        $target = "$targetPath/blogtut-$version/$language";

                        $this->generateIndexYii1($source, $target, $version, $unnormalizedLanguage, 'blog');

                        $this->stdout("done.\n", Console::BOLD, Console::FG_GREEN);
                    }
                }
            }
        }
    }


    /**
     * generate index file and populate elasticsearch index
     */
    protected function generateIndex($source, $target, $version, $language)
    {
        $guideController = new GuideController('guide', Yii::$app);

        $guideController->version = $version;
        $guideController->language = $language;
        $guideController->apiDocs = Yii::getAlias("@app/data/api-$version");

        $chapters = [];
        $sections = [];
        $data = $guideController->findRenderer(null)->loadGuideStructure([$source . '/README.md']);
        foreach ($data as $i => $chapter) {
            foreach ($chapter['content'] as $j => $section) {
                $file = basename($section['file'], '.md');
                if ($file === 'README') {
                    continue;
                }

                // index file
                $chapters[$chapter['headline']][$section['headline']] = $file;
                $sections[$file] = [$chapter['headline'], $section['headline']];

                // elasticsearch
                $file = $target . '/' . $file . '.html';
                if (!file_exists($file)) {
//                    echo "file not found: $file\n";
                    echo "f";
                    continue;
                }
                echo '.';
                $html = file_get_contents($file);
                SearchGuideSection::createRecord(basename($file, '.html'), $section['headline'], $html, $version, $language);
            }
        }
        $lines = file($source . '/README.md');
        if (($title = trim($lines[0])) === '') {
            $title = "The Definitive Guide for Yii {$version}";
        }

        FileHelper::createDirectory($target);
        file_put_contents("$target/index.data", serialize([$title, $chapters, $sections]));
    }


    protected function generateIndexYii1($source, $target, $version, $language, $type = 'guide')
    {
        $chapters = [];
        $sections = [];

        $file = "$source/toc.txt";
        $file = FileHelper::localize($file, $language, 'en');
        $lines = file($file);
        $chapter = '';
        foreach ($lines as $line) {
            // trim unicode BOM from line
            $line = trim(ltrim($line, "\xEF\xBB\xBF"));
            if ($line === '') {
                continue;
            }
            if ($line[0] === '*') {
                $chapter = trim($line, '* ');
            } else if ($line[0] === '-' && preg_match('/\[(.*?)\]\((.*?)\)/', $line, $matches)) {
                $chapters[$chapter][$matches[1]] = $matches[2];
                $sections[$matches[2]] = [$chapter, $matches[1]];

                // elasticsearch
                $file = $target . '/' . $matches[2] . '.html';
                if (!file_exists($file)) {
//                    echo "file not found: $file\n";
                    echo "f";
                    continue;
                }
                echo '.';
                $html = file_get_contents($file);
                SearchGuideSection::createRecord(basename($file, '.html'), $matches[1], $html, $version, $language, $type);
            }
        }

        $file = $type === 'blog' ? "$source/start.overview.txt" : "$source/index.txt";
        $file = FileHelper::localize($file, $language, 'en');
        $lines = file($file);
        if (($title = trim($lines[0])) === '') {
            $title = $type === 'blog' ? 'Building a Blog System Using Yii' : 'The Definitive Guide for Yii';
        }
        $title = str_replace('Yii', "Yii $version", $title);

        FileHelper::createDirectory($target);
        file_put_contents("$target/index.data", serialize([$title, $chapters, $sections]));
    }

    private function rebuildApiIndex()
    {
        $versions = Yii::$app->params['versions']['api'];
        foreach($versions as $version) {
            $targetPath = Yii::getAlias("@app/data/api-$version");
            $this->generateApiIndex($targetPath, $version);
        }
    }

    protected function generateApiIndex($target, $version)
    {
        $data = Json::decode(file_get_contents("$target/json/typeNames.json"));
        $count = count($data);
        $i = 0;
        Console::startProgress(0, $count, "Reindexing api $version ...");
        foreach ($data as $type) {
            SearchApiType::createRecord($type, $version);
            Console::updateProgress(++$i, $count);
        }
        Console::endProgress(true, true);

        $this->stdout("done.\n", Console::BOLD, Console::FG_GREEN);
    }

}
