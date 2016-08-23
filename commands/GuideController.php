<?php

namespace app\commands;

use app\apidoc\Yii1GuideRenderer;
use app\models\SearchGuideSection;
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

        try {
            // prepare elasticsearch index
            SearchGuideSection::setMappings();
            sleep(1);
        } catch (\Exception $e) {
            if (YII_DEBUG) {
                $this->stdout("!!! FAILED to prepare elasticsearch index !!! Search will not be available.\n", Console::FG_RED, Console::BOLD);
                $this->stdout(((string) $e) . "\n\n");
            } else {
                throw $e;
            }
        }

        if ($version[0] === '2') {
            foreach ($languages as $language => $name) {
                $source = "$sourcePath/yii-$version/docs/guide";
                if ($language !== 'en') {
                    if (strpos($language, '-') !== false) {
                        list($lang, $locale) = explode('-', $language);
                        $source .= "-$lang" . (empty($locale) ? '' : '-' . strtoupper($locale));
                    } else {
                        $source .= "-$language";
                    }
                }
                $target = "$targetPath/guide-$version/$language";
                $pdfTarget = "$targetPath/guide-$version/$language/pdf";
                $this->version = $version;
                $this->language = $language;
                $this->apiDocs = Yii::getAlias("@app/data/api-$version");

                $this->stdout("Start generating guide $version in $name...\n", Console::FG_CYAN);
                $this->template = 'bootstrap';
                $this->actionIndex([$source], $target);
                $this->generateIndex($source, $target);
                $this->stdout("Finished guide $version in $name.\n\n", Console::FG_CYAN);

                // set LaTeX language
                $languageMap = Yii::$app->params['guide-pdf.languages'];
                if (isset($languageMap[$language])) {
                    $this->stdout("Start generating guide $version PDF in $name...\n", Console::FG_CYAN);
                    $this->template = 'pdf';
                    $this->actionIndex([$source], $pdfTarget);

                    $this->stdout('Generating PDF with pdflatex...');
                    // adjust LaTeX config for language
                    if ($language === 'ja') {
                        // https://en.wikibooks.org/wiki/LaTeX/Internationalization#Japanese
                        // TODO this does not work yet. See https://github.com/yiisoft-contrib/yiiframework.com/issues/142
                        file_put_contents("$pdfTarget/main.tex", str_replace('\usepackage[british]{babel}', '\usepackage{japanese}', file_get_contents("$pdfTarget/main.tex")));
                    } elseif ($language === 'zh-cn') {
                        // https://en.wikibooks.org/wiki/LaTeX/Internationalization#Chinese
                        // TODO this does not work yet. See https://github.com/yiisoft-contrib/yiiframework.com/issues/142
                    } else {
                        file_put_contents("$pdfTarget/main.tex", str_replace('british', $languageMap[$language], file_get_contents("$pdfTarget/main.tex")));
                    }

                    if (file_exists("$pdfTarget/fail.log")) {
                        unlink("$pdfTarget/fail.log");
                    }
                    exec('cd ' . escapeshellarg($pdfTarget) . ' && make pdf', $output, $ret);
                    if ($ret === 0) {
                        $this->stdout("\nFinished guide $version PDF in $name.\n\n", Console::FG_CYAN);
                    } else {
                        $this->stdout("Guide $version PDF failed, make exited with status $ret.\n", Console::FG_RED);
                        file_put_contents("$pdfTarget/fail.log", implode("\n", $output));
                        $this->stdout("Errors logged to $pdfTarget/fail.log\n\n");
                    }
                } else {
                    $this->stdout("Guide PDF is not available for $name.\n\n", Console::FG_CYAN);
                }
            }
        }

        if ($version[0] === '1') {
            foreach ($languages as $language => $name) {
                $unnormalizedLanguage = strtolower(str_replace('-', '_', $language));

                $source = "$sourcePath/yii-$version/docs/guide";
                $target = "$targetPath/guide-$version/$language";
//                $pdfTarget = "$targetPath/guide-$version/$language/pdf"; TODO
                $this->version = $version;
                $this->language = $language;

                FileHelper::createDirectory($target);
                $renderer = new Yii1GuideRenderer([
                    'basePath' => $source,
                    'targetPath' => $target,
                ]);

                $this->stdout("Start generating guide $version in $name...\n", Console::FG_CYAN);
                $renderer->renderGuide($version, $unnormalizedLanguage);
                $this->generateIndexYii1($source, $target, $version, $unnormalizedLanguage);
                FileHelper::copyDirectory("$source/images", "$target/images");
                $this->stdout("Finished guide $version in $name.\n\n", Console::FG_CYAN);

            }

            // generate blog tutorial
            if (isset(Yii::$app->params['blogtut.versions'][$version])) {
                foreach(Yii::$app->params['blogtut.versions'][$version] as $language => $name) {
                    $unnormalizedLanguage = strtolower(str_replace('-', '_', $language));

                    $source = "$sourcePath/yii-$version/docs/blog";
                    $target = "$targetPath/blogtut-$version/$language";
//                $pdfTarget = "$targetPath/guide-$version/$language/pdf"; TODO
                    $this->version = $version;
                    $this->language = $language;

                    FileHelper::createDirectory($target);
                    $renderer = new Yii1GuideRenderer([
                        'basePath' => $source,
                        'targetPath' => $target,
                    ]);

                    $this->stdout("Start generating blog tutorial $version in $name...\n", Console::FG_CYAN);
                    $renderer->renderBlog($version, $unnormalizedLanguage);
                    $this->generateIndexYii1($source, $target, $version, $unnormalizedLanguage, 'blog');
                    FileHelper::copyDirectory("$source/images", "$target/images");
                    $this->stdout("Finished blog tutorial $version in $name.\n\n", Console::FG_CYAN);

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

    /**
     * generate index file and populate elasticsearch index
     */
    protected function generateIndex($source, $target)
    {
        $this->stdout('populating elasticsearch index...');

        try {

            // first delete all records for this version
            $version = $this->version;

            $chapters = [];
            $sections = [];
            $data = $this->findRenderer(null)->loadGuideStructure([$source . '/README.md']);
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
                        echo "file not found: $file\n";
                        continue;
                    }
                    $html = file_get_contents($file);
                    SearchGuideSection::createRecord(basename($file, '.html'), $section['headline'], $html, $this->version, $this->language);
                }
            }
            $lines = file($source . '/README.md');
            if (($title = trim($lines[0])) === '') {
                $title = "The Definitive Guide for Yii {$this->version}";
            }

            FileHelper::createDirectory($target);
            file_put_contents("$target/index.data", serialize([$title, $chapters, $sections]));

            $this->stdout("done.\n", Console::FG_GREEN);
        } catch (\Exception $e) {
            if (YII_DEBUG) {
                $this->stdout("!!! FAILED !!! Search will not be available.\n", Console::FG_RED, Console::BOLD);
                $this->stdout(((string) $e) . "\n\n");
            } else {
                throw $e;
            }
        }
    }

    protected function generateIndexYii1($source, $target, $version, $language, $type = 'guide')
    {
        $this->stdout('populating elasticsearch index...');

        try {
            // first delete all records for this version
            $version = $this->version;

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
                        echo "file not found: $file\n";
                        continue;
                    }
                    $html = file_get_contents($file);
                    SearchGuideSection::createRecord(basename($file, '.html'), $matches[1], $html, $this->version, $this->language, $type);
                }
            }

            $file = $type == 'blog' ? "$source/start.overview.txt" : "$source/index.txt";
            $file = FileHelper::localize($file, $language, 'en');
            $lines = file($file);
            if (($title = trim($lines[0])) === '') {
                $title = $type == 'blog' ? 'Building a Blog System Using Yii' : 'The Definitive Guide for Yii';
            }
            $title = str_replace('Yii', "Yii $version", $title);

            FileHelper::createDirectory($target);
            file_put_contents("$target/index.data", serialize([$title, $chapters, $sections]));

            $this->stdout("done.\n", Console::FG_GREEN);
        } catch (\Exception $e) {
            if (YII_DEBUG) {
                $this->stdout("!!! FAILED !!! Search will not be available.\n", Console::FG_RED, Console::BOLD);
                $this->stdout(((string) $e) . "\n\n");
            } else {
                throw $e;
            }
        }
    }
}
