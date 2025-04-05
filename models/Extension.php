<?php

namespace app\models;

use app\components\contentShare\EntityInterface;
use app\components\DiffBehavior;
use app\components\object\ClassType;
use app\components\object\ObjectIdentityInterface;
use app\components\packagist\Package;
use app\components\packagist\PackagistApi;
use app\components\UserPermissions;
use app\models\search\SearchableBehavior;
use app\models\search\SearchExtension;
use Composer\Spdx\SpdxLicenses;
use dosamigos\taggable\Taggable;
use Yii;
use yii\base\Exception;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\HttpException;

// TODO verify author via composer.json

/**
 * This is the model class for table "{{%extension}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $tagline
 * @property integer $category_id
 * @property integer $license_id
 * @property integer $from_packagist
 * @property integer $update_status
 * @property string $update_time
 * @property string $packagist_url
 * @property string $github_url
 * @property integer $owner_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $total_votes
 * @property integer $up_votes
 * @property double $rating
 * @property integer $featured
 * @property integer $comment_count
 * @property integer $download_count
 * @property string $yii_version
 * @property string $version_references
 * @property integer $status
 * @property string $description
 *
 * @property bool $isOfficialExtension
 *
 * @property User $owner
 * @property ExtensionCategory $category
 */
class Extension extends ActiveRecord implements Linkable, ObjectIdentityInterface, EntityInterface
{
    const STATUS_DRAFT = 1;
    const STATUS_PENDING_APPROVAL = 2;
    const STATUS_PUBLISHED = 3;
    const STATUS_DELETED = 5;

    const YII_VERSION_20 = '2.0';
    const YII_VERSION_11 = '1.1';
    const YII_VERSION_10 = '1.0';

    /**
     * Extension is new, no data has been populated.
     */
    const UPDATE_STATUS_NEW = 0;
    /**
     * Extension data is up to date.
     */
    const UPDATE_STATUS_UPTODATE = 1;
    /**
     * Extension data needs to be refreshed.
     */
    const UPDATE_STATUS_EXPIRED = 2;

    const NAME_PATTERN = '[a-z][a-z0-9\-]*';

    const TAGLINE_MAX_LENGTH = 128;
    const TAGLINE_PREVIEW_SUFFIX = '...';

    /**
     * @var string editor note on upate
     */
    public $memo;


    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(),
            'blameable' => [
                'class' => BlameableBehavior::class,
                //                'createdByAttribute' => 'owner_id', // TODO owner is must have and should not be changed
                //                'updatedByAttribute' => false, // TODO owner is must have and should not be changed
                'attributes' => [
                    static::EVENT_BEFORE_INSERT => 'owner_id',
                    //                    static::EVENT_BEFORE_UPDATE => 'updater_id',
                ],
            ],
            'taggable' => [
                'class' => Taggable::class,
            ],
            'search' => [
                'class' => SearchableBehavior::class,
                'searchClass' => SearchExtension::class,
            ],
            'diff' => DiffBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%extension}}';
    }

    public function initDefaults()
    {
        $this->description = <<<'MARKDOWN'

...overview of the extension...

## Requirements

...requirements of using this extension (e.g. Yii 2.0 or above)...

## Installation

...how to install the extension (e.g. composer install extensionname)...

## Usage

...how to use this extension...

...can use code blocks like the following...

```php
$model=new User;
$model->save();
```

## Resources

**DELETE THIS SECTION IF YOU DO NOT HAVE IT**

...external resources for this extension...

 * [Project page](URL to your project page)
 * [Try out a demo](URL to your project demo page)

MARKDOWN;

        $this->license_id = 'BSD-3-Clause';
        $this->update_status = self::UPDATE_STATUS_NEW;
        $this->tagline = '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'packagist_url', 'tagline', 'description'], 'filter', 'filter' => 'trim'],

            [['name', 'packagist_url', 'category_id', 'yii_version', 'license_id', 'tagline', 'description'], 'required'],

            ['name', 'match', 'pattern' => '/^' . self::NAME_PATTERN . '$/'],
            ['name', 'string', 'min' => 3, 'max' => 32],
            ['name', 'unique'],

            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtensionCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            ['license_id', 'validateLicenseId'], // spdx

            ['packagist_url', 'validatePackagistUrl'],

            [['description'], 'string'],
            [['tagline'], 'string', 'max' => 128],

            [['yii_version'], 'string', 'max' => 32],

            [['tagNames'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return [
            'create_packagist' => ['packagist_url', 'category_id', 'tagNames'],
            'create_custom' => ['name', 'category_id', 'github_url', 'yii_version', 'license_id', 'tagline', 'description', 'tagNames'],
            'update_packagist' => ['category_id', 'tagNames'],
            'update_custom' => ['category_id', 'github_url', 'yii_version', 'license_id', 'tagline', 'description', 'tagNames'],

        ];
    }

    public function validatePackagistUrl($attribute)
    {
        if (!is_string($this->$attribute)) {
            $this->addError($attribute, 'Packagist URL is invalid.');
            return;
        }
        $this->$attribute = static::normalizePackagistUrl($this->$attribute);
        $res = $this->parsePackagistUrl($this->$attribute);
        if ($res === false) {
            $this->addError($attribute, 'Packagist URL is invalid.');
        }
        if (($ext = static::find()->where(['name' => "{$res[0]}/{$res[1]}", 'from_packagist' => 1])->one()) !== null) {
            $this->addError($attribute, 'This Package has already been added: ' . Html::a(Html::encode($ext->name), $ext->getUrl()));
            return;
        }
        if (!(new PackagistApi)->getPackage($res[0], $res[1])) {
            $this->addError($attribute, 'The Package does not exist on Packagist.');
            return;
        }

        if ($res[0] === 'yiisoft' && !UserPermissions::canManageExtensions()) {
            $this->addError($attribute, 'You can not add packages made by yiisoft.');
        }
    }

    public static function normalizePackagistUrl($url)
    {
        if (Url::isRelative($url)) {
            $url = 'https://packagist.org/packages' . ltrim($url, '/');
        }
        if (strpos($url, 'http://') === 0) {
            $url = 'https://' . mb_substr($url, 7);
        }
        return rtrim($url, '/');
    }

    public function validateLicenseId($attribute)
    {
        if (!is_string($this->$attribute)) {
            $this->addError($attribute, 'License must be a valid SPDX License Identifier.');
            return;
        }
        if ($this->$attribute === 'other') {
            return;
        }

        $spdx = new SpdxLicenses();
        if (!$spdx->validate($this->$attribute)) {
            $this->addError($attribute, 'License must be a valid SPDX License Identifier.');
            return;
        }
    }

    public function getShowInSearch()
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'tagline' => 'Tagline',
            'category_id' => 'Category',
            'license_id' => 'License',
            'owner_id' => 'Owner',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'total_votes' => 'Total Votes',
            'up_votes' => 'Up Votes',
            'rating' => 'Rating',
            'featured' => 'Featured',
            'comment_count' => 'Comment Count',
            'download_count' => 'Download Count',
            'yii_version' => 'Yii Version',
            'status' => 'Status',
            'description' => 'Description',
            'tagNames' => 'Tags',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'owner_id']);
    }

    public function getIsOfficialExtension()
    {
        return $this->from_packagist && strncmp($this->name, 'yiisoft/', 8) === 0;
    }

    public function getOwnerLink()
    {
        return $this->getIsOfficialExtension() ? Html::a('The Yii Team', ['site/team']) : $this->owner->rankLink;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ExtensionCategory::class, ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(ExtensionTag::class, ['id' => 'extension_tag_id'])
            ->viaTable('extension2extension_tags', ['extension_id' => 'id']);
    }

    /**
     * @return FileQuery
     */
    public function getDownloads()
    {
        return $this->hasMany(File::class, ['object_id' => 'id'])->onCondition(['object_type' => 'Extension']);
    }

    /**
     * @inheritdoc
     * @return ExtensionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExtensionQuery(static::class);
    }

    public function getContentHtml()
    {
        $content = Yii::$app->formatter->asGuideMarkdown($this->description);

        if ($this->isOfficialExtension) {
            // replace guide link that works on github but not here
            $content = strtr($content, [
                'href="docs/guide/README.md"' => 'href="' . Html::encode(Url::to($this->getUrl('doc', ['type' => 'guide']))) . '"',
                'href="LICENSE.md"' => 'href="' . $this->github_url . '/blob/master/LICENSE.md"',
            ]);
        }

        return $content;
    }

    public function getLicenseLink()
    {
        $spdx = new SpdxLicenses();
        $license = $spdx->getLicenseByIdentifier($this->license_id);
        return $license === null ? Yii::$app->formatter->nullDisplay : Html::a($this->license_id, $license[2], ['title' => $license[0]]);
    }

    public static function getLicenseSelect()
    {
        $spdx = new SpdxLicenses();

        $identifiers = [
            'Apache-2.0',
            'EPL-1.0',
            'AGPL-3.0',
            'GPL-2.0',
            'GPL-3.0',
            'LGPL-3.0',
            'MIT',
            'MPL-1.1',
            'MPL-2.0',
            'BSD-2-Clause',
            'BSD-3-Clause',
            'PHP-3.0',
            'Sleepycat',
        ];
        $result = [];
        foreach ($identifiers as $i) {
            $license = $spdx->getLicenseByIdentifier($i);
            if ($license) {
                $result[$i] = $license[0];
            }
        }
        asort($result);
        $result['other'] = 'Other Open Source License';
        return $result;
    }

    /* TODO this will prevent users from manually adding the package, need to rethink
    public function beforeSave($insert)
    {
        // guess packagist name from README, if package is not fetched from github
        // this helps to prevent duplicate packages when packages are auto-added from packagist
        if (!$this->from_packagist && preg_match('~composer(?:\.phar)?\s+require.*([A-z0-9_.-]+/[A-z0-9_.-]+)~i',  $this->description, $matches)) {
            $this->packagist_url = static::normalizePackagistUrl($matches[1]);
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
    */

    /**
     * Extract vendor and package name from packagist URL
     * @param string $url
     * @return array|false
     */
    private function parsePackagistUrl($url)
    {
        $parts = parse_url($url);
        if (!$parts || !isset($parts['host']) || $parts['host'] !== 'packagist.org' || !isset($parts['path'])) {
            return false;
        }
        if (!preg_match('~^/p(?:ackages)?/([^/]+)/([^/]+)$~', $parts['path'], $matches)) {
            return false;
        }
        return [$matches[1], $matches[2]];
    }

    public function populatePackagistName()
    {
        $url = $this->parsePackagistUrl($this->packagist_url);
        if ($url === false) {
            throw new Exception('Can not load extension data from Packagist. Invalid packagist URL: ' . $this->packagist_url);
        }
        list($vendorName, $packageName) = $url;
        $this->name = "$vendorName/$packageName";
    }

    /**
     * Populate current model properties from packagist API.
     */
    public function populateFromPackagist()
    {
        $url = $this->parsePackagistUrl($this->packagist_url);
        if ($url === false) {
            throw new Exception('Can not load extension data from Packagist. Invalid packagist URL: ' . $this->packagist_url);
        }
        list($vendorName, $packageName) = $url;

        $keyCache = 'extension/package__package_' . md5(serialize([$vendorName, $packageName]));

        /** @var Package $package */
        $package = \Yii::$app->cache->get($keyCache);
        if ($package === false) {
            try {
                $package = (new PackagistApi())->getPackage($vendorName, $packageName);
            } catch (\Exception $e) {
                throw new HttpException(503, 'Packagist is currently unavailable: ' . $e->getMessage() . ' at ' . $e->getFile() . ' line ' . $e->getLine() . ': ' . $e->getTraceAsString(), 0, $e);
            }
            if (!$package) {
                throw new Exception('Package does not exist on Packagist.'); // TODO make this catchable for 404
            }
            \Yii::$app->cache->set($keyCache, $package, Yii::$app->params['cache.extensions.get']);
        }

        $this->tagline = mb_strlen($package->getDescription()) <= self::TAGLINE_MAX_LENGTH
            ? $package->getDescription()
            : mb_substr(
                $package->getDescription(),
                0,
                self::TAGLINE_MAX_LENGTH - mb_strlen(self::TAGLINE_PREVIEW_SUFFIX)
            ) . self::TAGLINE_PREVIEW_SUFFIX;
        $this->license_id = $package->getLicense();
        $this->yii_version = $package->getYiiVersion();
        $this->github_url = $package->getRepository();
        $this->version_references = Json::encode($package->getVersionReferences());

        $this->description = (new PackagistApi())->getReadmeFromRepository($package->getRepository());
        $downloads = $package->getDownloads();
        $this->download_count = $downloads['total'] ?? 0;
        $this->update_status = self::UPDATE_STATUS_UPTODATE;
        $this->update_time = date('Y-m-d H:i:s');
    }

    /**
     * Finds the related models based on shared tags.
     * @return static[] the related models
     */
    public function getRelatedExtensions($limit = 5)
    {
        $tags = $this->getTags()->all();
        if (empty($tags)) {
            return [];
        }
        $tagNames = array_values(ArrayHelper::map($tags, 'id', 'name'));

        $relatedIds = ExtensionTag::find()
            ->select(['extension_id', 'COUNT([[extension_id]]) AS [[similarity]]'])
            ->alias('t')
            ->leftJoin('extension2extension_tags e2t', 'e2t.extension_tag_id = t.id')
            ->where(['t.name' => $tagNames])->andWhere('extension_id != :id', [':id' => $this->id])
            ->groupBy('extension_id')
            ->orderBy(['similarity' => SORT_DESC, new Expression('RAND()')])
            ->limit($limit)
            ->asArray()->all();

        Yii::debug($relatedIds);

        $relatedIds = array_values(ArrayHelper::map($relatedIds, 'extension_id', 'extension_id'));
        return self::findAll($relatedIds);
    }

    /**
     * @return array url to this object. Should be something to be passed to [[\yii\helpers\Url::to()]].
     */
    public function getUrl($action = 'view', $params = [])
    {
        if ($this->from_packagist && strpos($this->name, '/') !== false) {
            list($vendor, $name) = explode('/', $this->name);
            $url = ["extension/$action", 'name' => $name, 'vendorName' => $vendor];
        } else {
            $url = ["extension/$action", 'name' => $this->name];
        }
        return empty($params) ? $url : array_merge($url, $params);
    }

    /**
     * @return string title to display for a link to this object.
     */
    public function getLinkTitle()
    {
        return $this->name;
    }

    /**
     * @return array Yii version list
     */
    public static function getYiiVersionOptions()
    {
        return [
            self::YII_VERSION_11 => '1.1',
            self::YII_VERSION_20 => '2.0',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (array_key_exists('status', $changedAttributes) && $changedAttributes['status'] != $this->status && (int) $this->status === self::STATUS_PUBLISHED) {
            ContentShare::addJobs($this);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function getObjectId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getObjectType()
    {
        return ClassType::EXTENSION;
    }

    /**
     * @inheritdoc
     */
    public function getContentShareTwitterMessage()
    {
        $url = Url::to($this->getUrl(), true);
        $text = '[extension] ' . $this->name . ': ' . $this->tagline;

        return StringHelper::truncate($text, 108) . " {$url} #yii";
    }

    public function cloneGitRepo($sourcePath, $gitRef)
    {
        if (!file_exists($sourcePath)) {
            passthru('git clone ' . escapeshellarg($this->github_url) . ' ' . escapeshellarg($sourcePath), $exitCode);
            if ($exitCode != 0) {
                return false;
            }
        } else {
            passthru('git -C ' . escapeshellarg($sourcePath) . ' fetch', $exitCode);
            if ($exitCode != 0) {
                return false;
            }
        }
        passthru('git -C ' . escapeshellarg($sourcePath) . ' checkout ' . escapeshellarg($gitRef), $exitCode);
        return $exitCode == 0;
    }

    public function hasApiDoc($version = null)
    {
        $versionsFile = Yii::getAlias("@app/data/extensions/{$this->name}/api.json");
        if (!is_file($versionsFile)) {
            return false;
        }
        $versions = Json::decode(file_get_contents($versionsFile));

        if (empty($versions)) {
            return false;
        }

        return $version === null || in_array($version, $versions, true);
    }

    public function getApiVersions()
    {
        $versionsFile = Yii::getAlias("@app/data/extensions/{$this->name}/api.json");
        if (!is_file($versionsFile)) {
            return [];
        }
        $versions = Json::decode(file_get_contents($versionsFile));

        if (empty($versions)) {
            return [];
        }

        return $versions;
    }

    public function hasGuideDoc($version = null)
    {
        $versionsFile = Yii::getAlias("@app/data/extensions/{$this->name}/guide.json");
        if (!is_file($versionsFile)) {
            return false;
        }
        $versions = Json::decode(file_get_contents($versionsFile));

        if (empty($versions)) {
            return false;
        }

        return $version === null || in_array($version, $versions, true);
    }

    /**
     * @return bool
     */
    public function isTaglineAPreview()
    {
        if (!$this->from_packagist) {
            return false;
        }

        return mb_substr($this->tagline, -mb_strlen(self::TAGLINE_PREVIEW_SUFFIX)) === self::TAGLINE_PREVIEW_SUFFIX;
    }
}
