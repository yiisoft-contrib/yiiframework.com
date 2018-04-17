<?php

namespace app\models;

use app\components\contentShare\EntityInterface;
use app\components\object\ClassType;
use app\components\object\ObjectIdentityInterface;
use app\components\SluggableBehavior;
use app\models\search\SearchableBehavior;
use app\models\search\SearchWiki;
use dosamigos\taggable\Taggable;
use Yii;
use yii\apidoc\helpers\ApiMarkdown;
use yii\base\Event;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\helpers\Markdown;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "wiki".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $contentHtml
 * @property int $total_votes
 * @property int $up_votes
 * @property float $rating
 * @property bool $featured
 * @property int $comment_count
 * @property int $status
 * @property integer $category_id
 * @property integer $creator_id
 * @property integer $updater_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $yii_version
 *
 * @property integer $view_count
 *
 * @property User $updater
 * @property User $creator
 * @property WikiRevision[] $revisions
 * @property WikiRevision[] $latestRevisions
 *
 */
class Wiki extends ActiveRecord implements Linkable, ObjectIdentityInterface, EntityInterface
{
    const STATUS_DRAFT = 1;
    const STATUS_PENDING_APPROVAL = 2;
    const STATUS_PUBLISHED = 3;
    const STATUS_DELETED = 5;

    const YII_VERSION_20 = '2.0';
    const YII_VERSION_11 = '1.1';
    const YII_VERSION_10 = '1.0';
    const YII_VERSION_ALL = 'all';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_LOAD = 'load';
    const SCENARIO_ADMIN = 'admin';

    /**
     * @var string editor note on upate
     */
    public $memo;
    /**
     * @var WikiRevision the revision saved after update
     */
    public $savedRevision;


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
            'slugable' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'attributes' => [
                    static::EVENT_BEFORE_INSERT => 'slug',
                    static::EVENT_BEFORE_UPDATE => 'slug',
                ],
            ],
            'tagable' => [
                'class' => Taggable::class,
            ],
            'search' => [
                'class' => SearchableBehavior::class,
                'searchClass' => SearchWiki::class,
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wiki}}';
    }

    /**
     * @return WikiQuery
     */
    public static function find()
    {
        return Yii::createObject(WikiQuery::class, [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'category_id', 'yii_version'], 'required'],
            [['content'], 'string'],
            [['category_id'], 'exist', 'targetClass' => WikiCategory::class, 'targetAttribute' => 'id'],
            [['title'], 'string', 'max' => 255],
            [['yii_version'], 'string', 'max' => 5],

            [['tagNames'], 'safe'],

            ['memo', 'required', 'on' => 'update'],
            ['status', 'integer']
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['title', 'content', 'category_id', 'yii_version', 'tagNames'],
            self::SCENARIO_UPDATE => ['title', 'content', 'category_id', 'yii_version', 'tagNames', 'memo'],
            self::SCENARIO_LOAD => ['title', 'content', 'category_id', 'yii_version', 'tagNames'],
            self::SCENARIO_ADMIN => ['status'],
        ];
    }

    public function getShowInSearch()
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    public function afterSave($insert, $changedAttributes)
    {
        // TODO do not store a revision if nothing has changed!
        // TODO make that a validation rule?
        $revision = new WikiRevision(['scenario' => WikiRevision::SCENARIO_CREATE]);
        $revision->wiki_id = $this->id;
        $revision->setAttributes($this->attributes);
        $revision->tagNames = $this->tagNames;
        $revision->memo = $insert ? null : $this->memo;
        $revision->updater_id = $insert ? $this->creator_id : $this->updater_id;
        $revision->save(false);
        $this->savedRevision = $revision;

        if (array_key_exists('status', $changedAttributes) && $changedAttributes['status'] != $this->status && (int) $this->status === self::STATUS_PUBLISHED) {
            ContentShare::addJobs($this);
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    public function transactions()
    {
        // enclose afterSave in transaction to ensure revision is stored together with the Wiki
        return [
            self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE,
        ];
    }

    public function loadRevision(WikiRevision $revision)
    {
        $oldScenario = $this->scenario;
        $this->scenario = self::SCENARIO_LOAD;
        $this->setAttributes($revision->attributes);
        $this->scenario = $oldScenario;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'category_id' => 'Category ID',
            'creator_id' => 'Creator ID',
            'updater_id' => 'Updater ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'yii_version' => 'Yii Version',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_DELETED => 'Deleted',
        ];
    }

    public function getContentHtml()
    {
        return Yii::$app->formatter->asGuideMarkdown($this->content);
    }

    public function getTeaser()
    {
        $paragraphs = preg_split("/\n\s+\n/", $this->content, -1, PREG_SPLIT_NO_EMPTY);
        while (count($paragraphs) > 0) {
            $teaser = array_shift($paragraphs);
            $teaser = StringHelper::truncate($teaser, 400);
            $html = Markdown::process($teaser, 'gfm');
            // do not use as teaser if the element in this block is a HTML block element
            if (!preg_match('~^<(blockquote|h\d|pre)~', $html)) {
                return HtmlPurifier::process($html);
            }
        }
        return '';
    }


//    protected $headings=array();
//    protected function processHeadings($match)
//    {
//        $level = intval($match[1]);
//        $id = 'hh'.count($this->headings);
//        $title = $match[2];
//
//        $this->headings[] = array('title' => $title, 'id' => $id, 'level'=>$level);
//        $anchor = sprintf('<a class="anchor" href="#%s">¶</a>', $id);
//        return sprintf('<h%d id="%s">%s %s</h%d>', $level, $id, $title, $anchor, $level);
//    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdater()
    {
        return $this->hasOne(User::className(), ['id' => 'updater_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisions()
    {
        return $this->hasMany(WikiRevision::className(), ['wiki_id' => 'id'])->orderBy(['revision' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLatestRevisions()
    {
        // this relation skips selecting content from the table for performance reasons
        return $this->getRevisions()->select(['wiki_id', 'revision', 'updater_id', 'updated_at', 'memo'])->limit(10);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(WikiCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(WikiTag::className(), ['id' => 'wiki_tag_id'])
            ->viaTable('wiki2wiki_tags', ['wiki_id' => 'id']);
    }

    /**
     * Finds the related models based on shared tags.
     * @return static[] the related models
     */
    public function getRelatedWikis($limit = 5)
    {
        $tags = $this->getTags()->all();
        if (empty($tags)) {
            return [];
        }
        $tagNames = array_values(ArrayHelper::map($tags, 'id', 'name'));

        $relatedIds = WikiTag::find()
            ->select(['wiki_id', 'COUNT([[wiki_id]]) AS [[similarity]]'])
            ->alias('t')
            ->leftJoin('wiki2wiki_tags w2t', 'w2t.wiki_tag_id = t.id')
            ->where(['t.name' => $tagNames])->andWhere('wiki_id != :id', [':id' => $this->id])
            ->groupBy('wiki_id')
            ->orderBy(['similarity' => SORT_DESC, new Expression('RAND()')])
            ->limit($limit)
            ->asArray()->all();

        Yii::trace($relatedIds);

        $relatedIds = array_values(ArrayHelper::map($relatedIds, 'wiki_id', 'wiki_id'));
        return Wiki::findAll($relatedIds);
    }

    /**
     * Event handler for comment creation. Update comment_count on wiki table.
     * @param Event $event
     */
    public static function onComment($event)
    {
        /** @var Comment $comment */
        $comment = $event->sender;
        if ($comment->object_type === ClassType::WIKI) {
            $count = Comment::find()->forObject(ClassType::WIKI, $comment->object_id)->active()->count();
            static::updateAll(['comment_count' => $count], ['id' => $comment->object_id]);
        }
    }

    /**
     * @return array url to this object. Should be something to be passed to [[\yii\helpers\Url::to()]].
     */
    public function getUrl($action = 'view', $params = [])
    {
        return array_merge($params, ["wiki/$action", 'id' => $this->id, 'name' => $this->slug]);
    }

    /**
     * @return string title to display for a link to this object.
     */
    public function getLinkTitle()
    {
        return $this->title;
    }

    /**
     * @return array Yii version list
     */
    public static function getYiiVersionOptions()
    {
        return [
            self::YII_VERSION_20 => 'Version 2.0',
            self::YII_VERSION_11 => 'Version 1.1',
            self::YII_VERSION_ALL => 'All Versions',
        ];
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
        return ClassType::WIKI;
    }

    /**
     * @inheritdoc
     */
    public function getContentShareTwitterMessage()
    {
        $url = Url::to($this->getUrl(), true);
        $text = '[wiki] ' . $this->getLinkTitle();

        $message = StringHelper::truncate($text, 108) . " {$url} #yii";

        return $message;
    }
}
