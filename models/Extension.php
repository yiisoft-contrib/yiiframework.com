<?php

namespace app\models;

use app\components\DiffBehavior;
use app\components\packagist\Package;
use app\components\packagist\PackagistApi;
use app\components\UserPermissions;
use Composer\Spdx\SpdxLicenses;
use dosamigos\taggable\Taggable;
use Imagine\Image\Profile;
use Yii;
use yii\base\Exception;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
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
 * @property integer $status
 * @property string $description
 *
 * @property User $owner
 * @property ExtensionCategory $category
 */
class Extension extends ActiveRecord implements Linkable, Tweetable
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

    /**
     * object type used for comments
     */
    const COMMENT_TYPE = 'Extension';
    /**
     * object type used for file uploads
     */
    const FILE_TYPE = 'Extension';

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
                'class' => Taggable::className(),
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

            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtensionCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
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
            $url = 'https://packagist.org/p/' . ltrim($url, '/');
        }
        if (strpos($url, 'http://') === 0) {
            $url = 'https://' . substr($url, 7);
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'tagline' => 'Tagline',
            'category_id' => 'Category ID',
            'license_id' => 'License ID',
            'owner_id' => 'Owner ID',
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ExtensionCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(ExtensionTag::className(), ['id' => 'extension_tag_id'])
            ->viaTable('extension2extension_tags', ['extension_id' => 'id']);
    }

    /**
     * @return FileQuery
     */
    public function getDownloads()
    {
        return $this->hasMany(File::className(), ['object_id' => 'id'])->onCondition(['object_type' => 'Extension']);
    }

    /**
     * @inheritdoc
     * @return ExtensionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExtensionQuery(get_called_class());
    }

    public function getContentHtml()
    {
        return Yii::$app->formatter->asGuideMarkdown($this->description);
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
                throw new HttpException(503, 'Packagist is currently unavailable.', 0, $e);
            }
            if (!$package) {
                throw new Exception('Package does not exist on Packagist.'); // TODO make this catchable for 404
            }
            \Yii::$app->cache->set($keyCache, $package, Yii::$app->params['cache.extensions.get']);

        }

        $this->tagline = $package->getDescription();
        $this->license_id = $package->getLicense();
        $this->yii_version = $package->getYiiVersion();
        $this->github_url = $package->getRepository();

        $this->description = (new PackagistApi())->getReadmeFromRepository($package->getRepository());
        $downloads = $package->getDownloads();
        $this->download_count = isset($downloads['total']) ? $downloads['total'] : 0;
        $this->update_status = self::UPDATE_STATUS_UPTODATE;
        $this->update_time = date('Y-m-d H:i:s');

//
//        if ($selectedVersion) {
//            foreach (['require', 'require-dev', 'suggest', 'provide', 'conflict', 'replace'] as $section) {
//                $selectedVersionData[$section] = [];
//
//                if (!empty($selectedVersion[$section])) {
//                    foreach ($selectedVersion[$section] as $kVersionItem => $vVersionItem) {
//                        $versionItemName = Html::encode($kVersionItem);
//                        if (preg_match('/^([\w\-\.]+)\/([\w\-\.]+)$/i', $kVersionItem, $match)) {
//                            $versionItemName = Html::a($versionItemName, [
//                                'extension/package',
//                                'vendorName' => $match[1],
//                                'packageName' => $match[2]
//                            ]);
//                        }
//
//                        $selectedVersionData[$section][] = $versionItemName . ': ' . Html::encode($vVersionItem);
//                    }
//                }
//            }
//        }
//
//        return $this->render(
//            'package',
//            [
//                'package' => $package,
//                'readme' => (new PackagistApi())->getReadmeFromRepository($package->getRepository()),
//                'versions' => $versions,
//                'selectedVersion' => $selectedVersion,
//                'selectedVersionData' => $selectedVersionData
//            ]
//        );

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

        Yii::trace($relatedIds);

        $relatedIds = array_values(ArrayHelper::map($relatedIds, 'extension_id', 'extension_id'));
        return Extension::findAll($relatedIds);
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
     * @return string the type of this object, e.g. News, Extension, Wiki
     */
    public function getItemType()
    {
        return static::COMMENT_TYPE;
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

    public function getTweetedObjectID()
    {
        return $this->id;
    }

    public function getTweetedObjectType()
    {
        return 'extension';
    }

    public function getTweetedText()
    {
        return '[extension] ' . $this->name . ': ' . $this->tagline . ' ' . $this->getUrl();
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ((int)$this->status === self::STATUS_PUBLISHED) {
            Tweet::fromTweetable($this)->enqueue();
        }

        parent::afterSave($insert, $changedAttributes);
    }


}
