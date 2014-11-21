<?php

namespace app\models;

use Yii;

class GuideSection
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
        if ($this->content === false) {
            if ($this->guide->language !== 'en') {
                $this->missingTranslation = true;
                $this->content = $this->loadContent($this->name, $this->guide->version, 'en');
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
            return "$chapter: $section | {$this->guide->title}";
        } else {
            return $this->guide->title;
        }
    }

    protected function loadContent($name, $version, $language)
    {
        $file = Yii::getAlias("@app/data/guide-$version/$language/$name.html");
        return @file_get_contents($file);
    }
}
