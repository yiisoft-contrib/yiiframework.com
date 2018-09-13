<?php

namespace app\models;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;

class Guide extends BaseObject
{
    const TYPE_BLOG = 'blogtut';
    const TYPE_GUIDE = 'guide';
    const TYPE_EXTENSION = 'ext';

    const LANGUAGE_EN = 'en';

    const VERSION_11 = '1.1';
    const VERSION_2 = '2';

    /**
     * @var string the version of this guide
     */
    public $version;
    /**
     * @var string the language ID of this guide
     */
    public $language;
    /**
     * @var Extension the extension this guide is for, if its an extension guide. null otherwise.
     */
    public $extension;
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
    public $type = self::TYPE_GUIDE;


    /**
     * Loads the guide with the specified version and language.
     * @param string $version
     * @param string $language
     * @return Guide the loaded guide, or null if the guide does not exist.
     */
    public static function load($version, $language, $type = self::TYPE_GUIDE)
    {
        if (!in_array($type, [self::TYPE_GUIDE, self::TYPE_BLOG], true)) {
            return null;
        }
        $guide = new self($version, $language, $type);
        if ($guide->validate() && $guide->loadIndex()) {
            return $guide;
        }

        return null;
    }

    public static function loadExtension(Extension $extension, $version, $language)
    {
        $guide = new self($version, $language, self::TYPE_EXTENSION);
        $guide->extension = $extension;
        if ($guide->validate() && $guide->loadIndex()) {
            return $guide;
        }
        return null;
    }

    /**
     * @param string $image image file name (without directory part)
     * @param string $version
     * @param string $language
     * @param string $type
     * @return bool|string the image file path, or false if the image file does not exist
     */
    public static function findImage($image, $version, $language, $type = self::TYPE_GUIDE)
    {
        if (!in_array($type, [self::TYPE_GUIDE, self::TYPE_BLOG], true)) {
            return false;
        }
        $versions = Yii::$app->params["$type.versions"];
        if (isset($versions[$version]) && isset($versions[$version][$language]) && preg_match('/^[\w\-\.]+\.(png|jpg|gif)$/i', $image)) {
            $file = Yii::getAlias("@app/data/$type-$version/$language/images/$image");
            return is_file($file) ? $file : false;
        }

        return false;
    }

    /**
     * @param string $image image file name (without directory part)
     * @return bool|string the image file path, or false if the image file does not exist
     */
    public function findExtensionImage($image)
    {
        if ($this->type !== self::TYPE_EXTENSION) {
            return false;
        }

        if (preg_match('/^[\w\-\.]+\.(png|jpg|gif)$/i', $image)) {
            $file = Yii::getAlias("@app/data/extensions/{$this->extension->name}/guide-{$this->version}/{$this->language}/images/$image");
            return is_file($file) ? $file : false;
        }

        return false;
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

    public function findSectionInOtherLanguages($name)
    {
        $result = [];
        foreach($this->getVersionOptions() as $version) {
            foreach($this->getLanguageOptions() as $language => $languageName) {

                $guide = Guide::load($version, $language, $this->type);
                if ($guide === null) {
                    continue;
                }
                $section = $guide->loadSection($name);
                if ($section === null) {
                    continue;
                }
                $result[$version][] = $section;
            }
        }

        return $result;
    }

    /**
     * @return array language ID => language name
     */
    public function getLanguageOptions()
    {
        if ($this->type === self::TYPE_EXTENSION) {
            $guideInfo = Yii::getAlias("@app/data/extensions/{$this->extension->name}/guide.json");
            if (!file_exists($guideInfo)) {
                return ['en' => 'English'];
            }
            $metadata = Json::decode(file_get_contents($guideInfo));
            $languages = ['en' => 'English'];
            foreach($metadata as $version => $langs) {
                foreach($langs as $lang) {
                    $languages[$lang] = \Locale::getDisplayLanguage($lang, $lang);
                }
            }
            return $languages;
        } else {
            return Yii::$app->params["{$this->type}.versions"][$this->version];
        }
    }

    /**
     * @return array version list
     */
    public function getVersionOptions()
    {
        if ($this->type === self::TYPE_EXTENSION) {
            $guideInfo = Yii::getAlias("@app/data/extensions/{$this->extension->name}/guide.json");
            if (!file_exists($guideInfo)) {
                return [];
            }
            $metadata = Json::decode(file_get_contents($guideInfo));
            $versions = array_keys($metadata);
        } else {
            $versions = array_keys(Yii::$app->params["{$this->type}.versions"]);
        }
        arsort($versions);
        return $versions;
    }

    /**
     * @return string the name of the language that this guide is in
     */
    public function getLanguageName()
    {
        $languages = $this->getLanguageOptions();
        return $languages[$this->language];
    }

    public function __construct($version, $language, $type = self::TYPE_GUIDE, $config = [])
    {
        parent::__construct($config);

        $this->version = $version;
        $this->language = $language;
        $this->type = $type;
    }

    protected function validate()
    {
        if ($this->type === self::TYPE_EXTENSION) {
            if (!$this->extension) {
                return false;
            }
            $guideInfo = Yii::getAlias("@app/data/extensions/{$this->extension->name}/guide.json");
            if (!file_exists($guideInfo)) {
                return false;
            }
            $metadata = Json::decode(file_get_contents($guideInfo));

            return isset($metadata[$this->version]) && in_array($this->language, $metadata[$this->version], true);
        } else {
            $versions = Yii::$app->params["{$this->type}.versions"];
            return isset($versions[$this->version]) && isset($versions[$this->version][$this->language]);
        }
    }

    protected function loadIndex()
    {
        if ($this->type === self::TYPE_EXTENSION) {
            $indexFile = Yii::getAlias("@app/data/extensions/{$this->extension->name}/guide-{$this->version}/{$this->language}/index.data");
        } else {
            $indexFile = Yii::getAlias("@app/data/{$this->type}-{$this->version}/{$this->language}/index.data");
        }
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
        return $this->type == self::TYPE_BLOG ? 'blog' : $this->type;
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
