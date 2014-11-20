<?php

namespace app\commands;

use Yii;
use yii\helpers\Console;
use app\apidoc\GuideRenderer;

/**
 * Generates the Definitive Guide for Yii.
 */
class GuideController extends \yii\apidoc\commands\GuideController
{
    public $defaultAction = 'generate';
    public $guidePrefix = '';
    protected $version = '2.0';
    protected $language = 'en';

    /**
     * Generates the Definitive Guide for the specified version of Yii.
     * @param string $version version number, such as 1.1, 2.0
     * @return integer exit status
     */
    public function actionGenerate($version)
    {
        $versions = Yii::$app->params['guide.versions'];
        if (!isset($versions[$version])) {
            $this->stderr("Unknown version $version. Valid versions are " . implode(', ', array_keys($versions)) . "\n\n", Console::FG_RED);
            return 1;
        }

        $languages = $versions[$version];
        $targetPath = Yii::getAlias('@app/data');
        $sourcePath = Yii::getAlias('@app/data');

        if ($version[0] === '2') {
            foreach ($languages as $language => $name) {
                $source = "$sourcePath/yii-$version/docs/guide";
                if ($language !== 'en') {
                    $source .= "-$language";
                }
                $target = "$targetPath/guide-$version/$language";
                $this->version = $version;
                $this->language = $language;
                $this->apiDocs = Yii::getAlias("@app/data/api-$version");

                $this->stdout("Start generating guide $version in $name...\n", Console::FG_CYAN);
                $this->generateIndex($source, $target);
                $this->actionIndex([$source], $target);
                $this->stdout("Finished guide $version in $name.\n\n", Console::FG_CYAN);
            }
        }

        return 0;
    }

    protected function findRenderer($template)
    {
        return new GuideRenderer([
            'guideUrl' => "/guide/{$this->version}/{$this->language}",
            'apiUrl' => "/api/{$this->version}",
        ]);
    }

    protected function generateIndex($source, $target)
    {
        $chapters = $this->findRenderer(null)->loadGuideStructure([$source . '/README.md']);
        $index = [];
        foreach ($chapters as $i => $chapter) {
            foreach ($chapter['content'] as $j => $section) {
                $section['file'] = basename($section['file'], '.md');
                $chapters[$i]['content'][$j] = $section;
                $index[$section['file']] = [$chapter['headline'], $section['headline']];
            }
        }
        $lines = file($source . '/README.md');
        if (($title = trim($lines[0])) === '') {
            $title = "The Definitive Guide for Yii {$this->version}";
        }

        file_put_contents("$target/index.data", serialize([$title, $chapters, $index]));
    }
}
