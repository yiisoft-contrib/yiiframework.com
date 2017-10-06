<?php

namespace app\commands;

use app\apidoc\Yii1GuideRenderer;
use app\models\SearchGuideSection;
use Yii;
use yii\helpers\Console;
use app\apidoc\GuideRenderer;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * Generates the Definitive Guide for Yii.
 */
class GuideController extends \yii\apidoc\commands\GuideController
{
    public $defaultAction = 'generate';
    public $guidePrefix = '';
    public $version = '2.0';
    public $language = 'en';

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
                $source = static::normalizeGuideDirectory($source, $language);
                $target = "$targetPath/guide-$version/$language";
                $pdfTarget = "$targetPath/guide-$version/$language/pdf";
                $this->version = $version;
                $this->language = $language;
                $this->apiDocs = Yii::getAlias("@app/data/api-$version");

                $this->stdout("Start generating guide $version in $name...\n", Console::FG_CYAN);
                $this->template = 'bootstrap';
                $this->actionIndex([$source], $target);
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

                    // adjust title for non english guides
                    if ($language !== 'en' && file_exists("$target/README.json")) {
                        $json = Json::decode(file_get_contents("$target/README.json"));
                        if (isset($json['h1'])) {
                            $tex = file_get_contents("$pdfTarget/main.tex");

                            $translators = '';
                            if (is_file("$source/translators.json")) {
                                $translatorNames = Json::decode(file_get_contents("$source/translators.json"));
                                if (!empty($translatorNames)) {
                                    $translatorNames = Inflector::sentence($translatorNames, ', \\\\\\\\', null, ', \\\\\\\\');
                                    $translators = "$name translation provided by: \\\\\\\\ " . $translatorNames;
                                }
                            }

                            $tex = preg_replace('~\\\\newcommand{\\\\plainTitle}{.+?}~', '\newcommand{\plainTitle}{' . $json['h1'] . '}', $tex);
                            $tex = preg_replace('~\\\\newcommand{\\\\formattedTitle}{.+? 2.0}~', '\newcommand{\formattedTitle}{' . $json['h1'] . '}', $tex);
                            $tex = preg_replace('~\\\\newcommand{\\\\formattedTranslators}{}~', '\newcommand{\formattedTranslators}{' . $translators . '}', $tex);

                            file_put_contents("$pdfTarget/main.tex", $tex);
                        }
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
                    FileHelper::copyDirectory("$source/images", "$target/images");
                    $this->stdout("Finished blog tutorial $version in $name.\n\n", Console::FG_CYAN);

                }
            }
        }

        return 0;
    }

    public static function normalizeGuideDirectory($source, $language)
    {
        if ($language === 'en') {
            return $source;
        }
        if (strpos($language, '-') !== false) {
            list($lang, $locale) = explode('-', $language);
            $source .= "-$lang" . (empty($locale) ? '' : '-' . strtoupper($locale));
        } else {
            $source .= "-$language";
        }
        return $source;
    }

    public function findRenderer($template)
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
}
