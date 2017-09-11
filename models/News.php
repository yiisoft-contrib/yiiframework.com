<?php

namespace app\models;

use dosamigos\taggable\Taggable;
use Yii;
use yii\apidoc\helpers\ApiMarkdown;
use yii\behaviors\BlameableBehavior;
use app\components\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $news_date
 * @property integer $image_id
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property integer $creator_id
 * @property integer $updater_id
 * @property integer $status
 *
 * @property NewsTag[] $tags
 * @property News[] $relatedNews
 * @property User $creator
 */
class News extends ActiveRecord implements Linkable
{
    const STATUS_DRAFT = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_DELETED = 3;

    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(),
            'blameable' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    static::EVENT_BEFORE_INSERT => 'creator_id',
                    static::EVENT_BEFORE_UPDATE => 'updater_id',
                ],
            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'attributes' => [
                    static::EVENT_BEFORE_INSERT => 'slug',
                    static::EVENT_BEFORE_UPDATE => 'slug',
                ],
            ],
            [
                'class' => Taggable::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // filters
            [['title'], 'trim'],

            // validators
            [['title', 'content', 'status', 'news_date'], 'required'],

            [['title'], 'string', 'max' => 128],
            [['content'], 'string'],

            ['status', 'in', 'range' => array_keys(static::getStatusList())],

            [['news_date'], 'date', 'timestampAttribute' => 'news_date', 'timestampAttributeFormat' => 'php:Y-m-d'],

            [['tagNames'], 'safe'],
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_DELETED => 'Deleted',
        ];
    }

    public function getStatusName()
    {
        $statusList = static::getStatusList();
        return isset($statusList[$this->status]) ? $statusList[$this->status] : 'Unknown';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'news_date' => 'News Date',
            'image_id' => 'Image ID',
            'content' => 'Content',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'creator_id' => 'Creator ID',
            'updater_id' => 'Updater ID',
            'status' => 'Status',
            'statusName' => 'Status',
        ];
    }

    public function getTeaser()
    {
        $lines = preg_split("/\n\s+\n/", $this->content, -1, PREG_SPLIT_NO_EMPTY);
        return reset($lines);
    }

    /**
     * @inheritdoc
     * @return NewsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NewsQuery(get_called_class());
    }

    public function getContentHtml()
    {
        ApiMarkdown::$renderer = new \app\apidoc\GuideRenderer();
        ApiMarkdown::$renderer->apiContext = new \yii\apidoc\models\Context();
        return ApiMarkdown::process($this->content);
    }

    // relations

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(NewsTag::className(), ['id' => 'news_tag_id'])
            ->viaTable('news2news_tags', ['news_id' => 'id']);
    }

    /**
     * @return News[]
     */
    public function getRelatedNews()
    {
        $tags = $this->tags;
        if (empty($tags)) {
            return [];
        }
        $likes = [];
        foreach($tags as $i => $tag) {
            if ($i > 5) {
                break;
            }
            $likes[] = $tag->name;
        }
        $ids = News::find()
            ->latest()
            ->published()
            ->select('news.id')->distinct()
            ->joinWith('tags AS tags')
            ->where(['or like', 'tags.name', $likes])
            ->andWhere(['!=', 'news.id', $this->id])
            ->limit(5)
            ->column();

        return News::findAll($ids);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * @return array url to this object. Should be something to be passed to [[\yii\helpers\Url::to()]].
     */
    public function getUrl($action = 'view', $params = [])
    {
        return ['news/view', 'id' => $this->id, 'name' => $this->slug];
    }

    /**
     * @return string title to display for a link to this object.
     */
    public function getLinkTitle()
    {
        return $this->title;
    }

    /**
     * @return string the type of this object, e.g. News, Extension, Wiki
     */
    public function getItemType()
    {
        return 'News';
    }
}
