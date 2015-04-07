<?php

namespace app\commands;

use Yii;
use yii\helpers\Console;
use app\apidoc\GuideRenderer;
use yii\helpers\FileHelper;

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
                $pdfTarget = "$targetPath/guide-$version/$language/pdf";
                $this->version = $version;
                $this->language = $language;
                $this->apiDocs = Yii::getAlias("@app/data/api-$version");

                $this->stdout("Start generating guide $version in $name...\n", Console::FG_CYAN);
                $this->template = 'bootstrap';
                $this->generateIndex($source, $target);
                $this->actionIndex([$source], $target);
                $this->stdout("Finished guide $version in $name.\n\n", Console::FG_CYAN);

                // set LaTeX language
                $languageMap = [
                    'en' => 'british',
                    'de' => 'ngerman',
                    'ru' => 'russian',
                ];
                if (isset($languageMap[$language])) {
                    $this->stdout("Start generating guide $version PDF in $name...\n", Console::FG_CYAN);
                    $this->template = 'pdf';
                    $this->actionIndex([$source], $pdfTarget);
                    $this->stdout('Generating PDF with pdflatex...');
                    file_put_contents("$pdfTarget/main.tex", str_replace('british', $languageMap[$language], file_get_contents("$pdfTarget/main.tex")));
                    exec('cd ' . escapeshellarg($pdfTarget) . ' && make pdf', $output, $ret);
                    if ($ret === 0) {
                        $this->stdout("\nFinished guide $version PDF in $name.\n\n", Console::FG_CYAN);
                    } else {
                        $this->stdout("\n" . implode("\n", $output) . "\n");
                        $this->stdout("Guide $version PDF failed, make exited with status $ret.\n\n", Console::FG_RED);
                    }
                } else {
                    $this->stdout("Guide PDF is not available for $name.\n\n", Console::FG_CYAN);
                }
            }
        }

        return 0;
    }

    protected function findRenderer($template)
    {
        if ($template === 'pdf') {
            $rendererClass = 'yii\\apidoc\\templates\\' . $template . '\\GuideRenderer';
            return new $rendererClass();
        }

        return new GuideRenderer([
            'guideUrl' => Yii::$app->params['guide.baseUrl'] . "/{$this->version}/{$this->language}",
            'apiUrl' => Yii::$app->params['api.baseUrl'] .  "/{$this->version}",
        ]);
    }

    protected function generateIndex($source, $target)
    {
        $chapters = [];
        $sections = [];
        $data = $this->findRenderer(null)->loadGuideStructure([$source . '/README.md']);
        foreach ($data as $i => $chapter) {
            foreach ($chapter['content'] as $j => $section) {
                $file = basename($section['file'], '.md');
                if ($file === 'README') {
                    continue;
                }
                $chapters[$chapter['headline']][$section['headline']] = $file;
                $sections[$file] = [$chapter['headline'], $section['headline']];
            }
        }
        $lines = file($source . '/README.md');
        if (($title = trim($lines[0])) === '') {
            $title = "The Definitive Guide for Yii {$this->version}";
        }

        FileHelper::createDirectory($target);
        file_put_contents("$target/index.data", serialize([$title, $chapters, $sections]));
    }
}
