<?php

namespace app\models;

use Yii;
use yii\base\Object;

class GuideSection extends Object
{
    public $name;
    public $version;
    public $language;
    protected $filePath;
    protected $missingTranslation = false;

    public function __construct($name, $version, $language, $config = [])
    {
        $this->name = $name;
        $this->version = $version;
        $this->language = $language;
        parent::__construct($config);
    }

    public function validate()
    {
        $versions = Yii::$app->params['guide.versions'];
        if (!isset($versions[$this->version]) || !isset($versions[$this->version][$this->language]) || !preg_match('/^[\w\-\.]+$/', $this->name)) {
            return false;
        }

        $file = $this->getSectionFile();
        if (!is_file($file) && $this->language !== 'en') {
            $file = $this->getSectionFile($this->name, $this->version, 'en');
            $this->missingTranslation = true;
        }
        $this->filePath = $file;

        return is_file($file);
    }

    public function getContent()
    {
        return file_get_contents($this->filePath);
    }

    public function getLanguageOptions()
    {
        return Yii::$app->params['guide.versions'][$this->version];
    }

    public function getVersionOptions()
    {
        return array_keys(Yii::$app->params['guide.versions']);
    }

    public function getSectionFile($name = null, $version = null, $language = null)
    {
        if ($name === null) {
            $name = $this->name;
        }
        if ($version === null) {
            $version = $this->version;
        }
        if ($language === null) {
            $language = $this->language;
        }
        return Yii::getAlias("@app/data/guide-$version/$language/$name.html");
    }

    protected $index;

    protected function getGuideIndex()
    {
        if ($this->index === null) {
            $indexFile = Yii::getAlias("@app/data/guide-{$this->version}/{$this->language}/index.data");
            $this->index = unserialize(file_get_contents($indexFile));
        }
        return $this->index;
    }

    public function getPageTitle()
    {
        list ($title, $chapters, $sections) = $this->getGuideIndex();
        list ($chapter, $section) = $sections[$this->name];
        return "$chapter: $section | $title";
    }

    public function getGuideChapters()
    {
        list ($title, $chapters, $sections) = $this->getGuideIndex();
        return $chapters;
    }

    public function getLanguageName()
    {
        $languages = $this->getLanguageOptions();
        return $languages[$this->language];
    }
}
