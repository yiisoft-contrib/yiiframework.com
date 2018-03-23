<?php

namespace app\apidoc;

use app\models\SearchGuideSection;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Console;
use yii\helpers\Json;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class GuideRenderer extends \yii\apidoc\templates\html\GuideRenderer
{
    use RendererTrait;

    public $layout = false;

    protected $targetDir;


    public function generateGuideUrl($file)
    {
        $hash = '';
        if (($pos = strrpos($file, '#')) !== false) {
            $hash = substr($file, $pos);
            $file = substr($file, 0, $pos);
        }
        return rtrim($this->guideUrl, '/') . '/' . $this->guidePrefix . basename($file, '.md') . $hash;
    }

    public function generateApiUrl($typeName)
    {
        return rtrim($this->apiUrl, '/') . '/' . strtolower(str_replace('\\', '-', $typeName));
    }

    public function render($files, $targetDir)
    {
        $this->targetDir = $targetDir;
        return parent::render($files, $targetDir);
    }

    protected function afterMarkdownProcess($file, $output, $renderer)
    {
        $output = $this->fixMarkdownLinks($output);

        // add toc CSS class to be hidden on large screens
        $output = str_replace('<div class="toc">', '<div class="toc hidden-lg">', $output);

        // extract toc as json
        $headings = [
            'h1' => '',
            'id' => '',
            'sections' => $renderer->getHeadings(),
        ];
        if (preg_match('/<h1>(.+?)(\s*<span id="(.+?)">.*?)?<\/h1>/i', $output, $matches)) {
            $headings['h1'] = $matches[1];
            $headings['id'] = isset($matches[3]) ? $matches[3] : '';
        }
        try {
            file_put_contents($this->targetDir . '/' . basename($file, 'md') . 'json', Json::encode($headings));
        } catch (InvalidArgumentException $e) {
            throw new \yii\base\Exception("JSON error while storing structure of file '$file': " . $e->getMessage(), $e->getCode(), $e);
        }

        // replace heading ids <span id=> => <hX id=>
        $output = preg_replace('/<h(\d)>(.+?)(<span id="(.+?)"><\/span>)(.*?)<\/h\1>/i', '<h\1 id="\4">\2\5</h\1>', $output);

        return $output;
    }

    protected function fixMarkdownLinks($content)
    {
        $guideUrl = rtrim($this->guideUrl, '/');
        $content = preg_replace('/href\s*=\s*"([^"\/]+)\.md(#.*)?"/i', "href=\"$guideUrl/\\1\\2\"", $content);
        return preg_replace('%<img src="(images/[^"]+)"%', "<img class=\"img-responsive\" src=\"$guideUrl/\\1\"", $content);
    }

    public function loadGuideStructure($files)
    {
        return parent::loadGuideStructure($files);
    }
}
