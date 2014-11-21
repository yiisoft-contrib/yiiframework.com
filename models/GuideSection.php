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
        $file = Yii::getAlias("@app/data/guide-$version/$language/$name.html");
        return @file_get_contents($file);
    }
}
