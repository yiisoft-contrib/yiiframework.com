<?php

namespace app\models;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;

class GuideSection extends BaseObject
{
    /**
     * @var Guide
     */
    public $guide;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $content;
    /**
     * @var string
     */
    public $headings = [];
    /**
     * @var boolean
     */
    public $missingTranslation;


    public function __construct($name, Guide $guide, $config = [])
    {
        parent::__construct($config);

        $this->name = $name;
        $this->guide = $guide;
    }

    /**
     * Loads the section content from its file.
     * @return boolean whether the loading is successful
     */
    public function load()
    {
        $this->content = $this->loadContent($this->name, $this->guide->version, $this->guide->language);
        $this->headings = $this->loadHeadings($this->name, $this->guide->version, $this->guide->language);
        if ($this->content === false) {
            if ($this->guide->language !== Guide::LANGUAGE_EN) {
                $this->missingTranslation = true;
                $this->content = $this->loadContent($this->name, $this->guide->version, Guide::LANGUAGE_EN);
                $this->headings = $this->loadHeadings($this->name, $this->guide->version, Guide::LANGUAGE_EN);
            }
        }
        return $this->content !== false;
    }

    public function hasTranslation($language)
    {
        if ($this->guide->type === Guide::TYPE_EXTENSION) {
            $translationGuide = Guide::loadExtension($this->guide->extension, $this->guide->version, $language);
        } else {
            $translationGuide = Guide::load($this->guide->version, $language, $this->guide->type);
        }
        if (!$translationGuide) {
            return false;
        }
        return $translationGuide->loadSection($this->name) !== null;
    }

    /**
     * @return string the title is suitable for being used as a page title
     */
    public function getPageTitle()
    {
        if (isset($this->guide->sections[$this->name])) {
            list ($chapter, $section) = $this->guide->sections[$this->name];
            return "$chapter: $section";
        }

        return $this->guide->title;
    }

    /**
     * @return string the title of the section
     */
    public function getTitle()
    {
        if (!empty($this->headings['h1'])) {
            return $this->headings['h1'];
        }
        if (isset($this->guide->sections[$this->name])) {
            list ($chapter, $section) = $this->guide->sections[$this->name];
            return $section;
        }

        return false;
    }

    /**
     * @return array the previous section information ([name, title]), or null if the current section is the first one
     */
    public function getPrevSection()
    {
        $names = array_keys($this->guide->sections);
        $index = array_search($this->name, $names);
        if ($index - 1 >= 0) {
            $name = $names[$index - 1];
            list ($chapter, $section) = $this->guide->sections[$name];
            if ($this->guide->sections[$this->name][0] === $chapter) {
                return [$name, $section];
            }

            return [$name, "$chapter: $section"];
        }
        return null;
    }

    /**
     * @return array the next section information ([name, title]), or null if the current section is the last one
     */
    public function getNextSection()
    {
        $names = array_keys($this->guide->sections);
        $index = array_search($this->name, $names);
        if ($index >= 0 && $index + 1 < count($names)) {
            $name = $names[$index + 1];
            list ($chapter, $section) = $this->guide->sections[$name];
            if ($this->guide->sections[$this->name][0] === $chapter) {
                return [$name, $section];
            }

            return [$name, "$chapter: $section"];
        }
        return null;
    }

    protected function loadContent($name, $version, $language)
    {
        if ($this->guide->type === Guide::TYPE_EXTENSION) {
            $file = Yii::getAlias("@app/data/extensions/{$this->guide->extension->name}/guide-$version/$language/$name.html");
        } else {
            $file = Yii::getAlias("@app/data/{$this->guide->type}-$version/$language/$name.html");
        }
        return @file_get_contents($file);
    }

    protected function loadHeadings($name, $version, $language)
    {
        if ($this->guide->type === Guide::TYPE_EXTENSION) {
            $file = Yii::getAlias("@app/data/extensions/{$this->guide->extension->name}/guide-$version/$language/$name.json");
        } else {
            $file = Yii::getAlias("@app/data/{$this->guide->type}-$version/$language/$name.json");
        }
        $json = @file_get_contents($file);
        return empty($json) ? [] : Json::decode($json);
    }

    public function getEditUrl()
    {
        $version = $this->guide->version;
        if ($version === Guide::VERSION_11) {
            if ($this->missingTranslation) {
                $language = 'en';
            } else {
                $language = str_replace('-', '_', strtolower($this->guide->language));
            }
            $type = $this->guide->type == Guide::TYPE_BLOG ? 'blog' : 'guide';
            return "https://github.com/yiisoft/yii/edit/master/docs/{$type}/" . ($language !== Guide::LANGUAGE_EN ? "$language/" : '') . "{$this->name}.txt";
        }

        if ($version[0] === Guide::VERSION_2) {
            if ($this->missingTranslation) {
                $language = Guide::LANGUAGE_EN;
            } elseif (strpos($this->guide->language, '-') !== false) {
                list($lang, $locale) = explode('-', $this->guide->language);
                $language = $lang . (empty($locale) ? '' : '-' . strtoupper($locale));
            } else {
                $language = $this->guide->language;
            }
            return 'https://github.com/yiisoft/yii2/edit/master/docs/guide' . ($language !== Guide::LANGUAGE_EN ? "-$language" : '') . "/{$this->name}.md";
        }
        return false;
    }
}
