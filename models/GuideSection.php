<?php

namespace app\models;

use Yii;
use yii\base\Object;
use yii\helpers\Json;

class GuideSection extends Object
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


    public function __construct($name, Guide $guide)
    {
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
            if ($this->guide->language !== 'en') {
                $this->missingTranslation = true;
                $this->content = $this->loadContent($this->name, $this->guide->version, 'en');
                $this->headings = $this->loadHeadings($this->name, $this->guide->version, 'en');
            }
        }
        return $this->content !== false;
    }

    /**
     * @return string the title is suitable for being used as a page title
     */
    public function getPageTitle()
    {
        if (isset($this->guide->sections[$this->name])) {
            list ($chapter, $section) = $this->guide->sections[$this->name];
            return "$chapter: $section";
        } else {
            return $this->guide->title;
        }
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
        } else {
            return false;
        }
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
            } else {
                return [$name, "$chapter: $section"];
            }
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
            } else {
                return [$name, "$chapter: $section"];
            }
        }
        return null;
    }

    protected function loadContent($name, $version, $language)
    {
        $file = Yii::getAlias("@app/data/{$this->guide->type}-$version/$language/$name.html");
        return @file_get_contents($file);
    }

    protected function loadHeadings($name, $version, $language)
    {
        $file = Yii::getAlias("@app/data/{$this->guide->type}-$version/$language/$name.json");
        $json = @file_get_contents($file);
        return empty($json) ? [] : Json::decode($json);
    }

    public function getEditUrl()
    {
        $version = $this->guide->version;
        if ($version === '1.1') {
            if ($this->missingTranslation) {
                $language = 'en';
            } else {
                $language = str_replace('-', '_', strtolower($this->guide->language));
            }
            $type = $this->guide->type == 'blogtut' ? 'blog' : 'guide';
            return "https://github.com/yiisoft/yii/edit/master/docs/{$type}/" . ($language !== 'en' ? "$language/" : '') . "{$this->name}.txt";
        } elseif ($version[0] === '2') {
            if ($this->missingTranslation) {
                $language = 'en';
            } elseif (strpos($this->guide->language, '-') !== false) {
                list($lang, $locale) = explode('-', $this->guide->language);
                $language = $lang . (empty($locale) ? '' : '-' . strtoupper($locale));
            } else {
                $language = $this->guide->language;
            }
            return 'https://github.com/yiisoft/yii2/edit/master/docs/guide' . ($language !== 'en' ? "-$language" : '') . "/{$this->name}.md";
        }
        return false;
    }
}
