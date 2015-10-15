<?php

namespace app\models;

use Yii;
use yii\base\Object;

class Guide extends Object
{
    /**
     * @var string the version of this guide
     */
    public $version;
    /**
     * @var string the language ID of this guide
     */
    public $language;
    /**
     * @var string the title of this guide
     */
    public $title;
    /**
     * @var array chapters in this guide (chapter title, section title => section name)
     */
    public $chapters = [];
    /**
     * @var array all sections in this guide (section name => [chapter title, section title])
     */
    public $sections = [];
    /**
     * @var string the type of guide, e.g. 'guide' or 'blogtut'
     */
    public $type = 'guide';


    /**
     * Loads the guide with the specified version and language.
     * @param string $version
     * @param string $language
     * @return Guide the loaded guide, or null if the guide does not exist.
     */
    public static function load($version, $language, $type = 'guide')
    {
        if (!in_array($type, ['guide', 'blogtut'])) {
            return null;
        }
        $guide = new self($version, $language, $type);
        if ($guide->validate() && $guide->loadIndex()) {
            return $guide;
        } else {
            return null;
        }
    }

    /**
     * @param string $image image file name (without directory part)
     * @param string $version
     * @param string $language
     * @return bool|string the image file path, or false if the image file does not exist
     */
    public static function findImage($image, $version, $language, $type = 'guide')
    {
        if (!in_array($type, ['guide', 'blogtut'])) {
            return false;
        }
        $versions = Yii::$app->params["$type.versions"];
        if (isset($versions[$version]) && isset($versions[$version][$language]) && preg_match('/^[\w\-\.]+\.(png|jpg|gif)$/i', $image)) {
            $file = Yii::getAlias("@app/data/$type-$version/$language/images/$image");
            return is_file($file) ? $file : false;
        } else {
            return false;
        }
    }

    /**
     * @param string $name section name
     * @return GuideSection|null the guide section, or null if the section does not exist
     */
    public function loadSection($name)
    {
        if (isset($this->sections[$name])) {
            $section = new GuideSection($name, $this);
            if ($section->load()) {
                return $section;
            }
        }
        return null;
    }

    /**
     * @return array language ID => language name
     */
    public function getLanguageOptions()
    {
        return Yii::$app->params["{$this->type}.versions"][$this->version];
    }

    /**
     * @return array version list
     */
    public function getVersionOptions()
    {
        return array_keys(Yii::$app->params["{$this->type}.versions"]);
    }

    /**
     * @return string the name of the language that this guide is in
     */
    public function getLanguageName()
    {
        $languages = $this->getLanguageOptions();
        return $languages[$this->language];
    }

    public function __construct($version, $language, $type = 'guide')
    {
        $this->version = $version;
        $this->language = $language;
        $this->type = $type;
    }

    protected function validate()
    {
        $versions = Yii::$app->params["{$this->type}.versions"];
        return isset($versions[$this->version]) && isset($versions[$this->version][$this->language]);
    }

    protected function loadIndex()
    {
        $indexFile = Yii::getAlias("@app/data/{$this->type}-{$this->version}/{$this->language}/index.data");
        if (is_file($indexFile)) {
            $index = @unserialize(file_get_contents($indexFile));
            if (count($index) === 3) {
                list ($this->title, $this->chapters, $this->sections) = $index;
                return true;
            }
        }
        return false;
    }

    public function getTypeUrlName()
    {
        return $this->type == 'blogtut' ? 'blog' : $this->type;
    }

    public function getDownloadFile($format)
    {
        if ($this->version[0] == '2') {
            switch($format) {
                case 'pdf':
                    $file = Yii::getAlias("@app/data/guide-{$this->version}/{$this->language}/pdf/guide.pdf");
                    $name = "yii-guide-{$this->version}-{$this->language}.pdf";
                    break;
                case 'tar.gz':
                case 'tar.bz2':
                    $lang = $this->localeToUpper($this->language);
                    $file = Yii::getAlias("@app/data/docs-offline/yii-docs-{$this->version}-{$lang}.$format");
                    $name = "yii-docs-{$this->version}-{$this->language}.$format";
                    break;
                default:
                    return false;
            }
            if (is_file($file)) {
                return [
                    'file' => $file,
                    'name' => $name,
                ];
            }
        }
        return false;
    }

    public function localeToUpper($locale)
    {
        $l = explode('-', $locale);
        if (isset($l[1])) {
            $l[1] = strtoupper($l[1]);
        }
        return implode('-', $l);
    }
}
