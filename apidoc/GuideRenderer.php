<?php

namespace app\apidoc;

use Yii;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class GuideRenderer extends \yii\apidoc\templates\html\GuideRenderer
{
    use RendererTrait;

    public $layout = false;

    public function generateGuideUrl($file)
    {
        $hash = '';
        if (($pos = strrpos($file, '#')) !== false) {
            $hash = substr($file, $pos);
            $file = substr($file, 0, $pos);
        }
        return rtrim($this->guideUrl, '/') . '/' . $this->guidePrefix . basename($file, '.md') . $hash;
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
