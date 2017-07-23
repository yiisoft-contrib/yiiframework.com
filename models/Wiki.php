<?php

namespace app\models;

use app\components\SluggableBehavior;
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

/**
 * This is the model class for table "wiki".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $content
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
 * @property string $contentHtml
 */
class Wiki extends ActiveRecord implements Linkable
{
    const STATUS_DRAFT = 1;
    const STATUS_PENDING_APPROVAL = 2;
    const STATUS_PUBLISHED = 3;
    const STATUS_DELETED = 5;

    /**
     * object type used for wiki comments
     */
    const COMMENT_TYPE = 'wiki';

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
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => 'updater_id',
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
                'class' => Taggable::className(),
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
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['title', 'content', 'category_id', 'yii_version', 'tagNames'],
            'update' => ['title', 'content', 'category_id', 'yii_version', 'tagNames', 'memo'],
            'load'   => ['title', 'content', 'category_id', 'yii_version', 'tagNames'],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        // TODO do not store a revision if nothing has changed!
        // TODO make that a validation rule?
        $revision = new WikiRevision(['scenario' => 'create']);
        $revision->wiki_id = $this->id;
        $revision->setAttributes($this->attributes);
        $revision->memo = $insert ? null : $this->memo;
        $revision->save(false);
        $this->savedRevision = $revision;

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
        $this->scenario = 'load';
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

    public function getContentHtml()
    {
        return Yii::$app->formatter->asGuideMarkdown($this->content);
    }

    public function getTeaser()
    {
        $paragraphs = preg_split("/\n\s+\n/", $this->content, -1, PREG_SPLIT_NO_EMPTY);
        while(count($paragraphs) > 0) {
            $teaser = array_shift($paragraphs);
            $teaser = StringHelper::truncate($teaser, 400);
            $html = Markdown::process($teaser, 'gfm');
            // do not use as teaser if the element in this block is a HTML block element
            if (!preg_match('~^<(blockquote|h\d|pre)~', $html)) {
                return $html;
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
        /** @var $comment Comment */
        $comment = $event->sender;
        if ($comment->object_type === Wiki::COMMENT_TYPE) {
            $count = Comment::find()->forObject(Wiki::COMMENT_TYPE, $comment->object_id)->active()->count();
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
}
