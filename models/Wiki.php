<?php

namespace app\models;

use app\components\SluggableBehavior;
use dosamigos\taggable\Taggable;
use Yii;
use yii\apidoc\helpers\ApiMarkdown;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
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
class Wiki extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_DELETED = 3;

    /**
     * @var string editor note on upate
     */
    public $memo;


    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'created_at', // do not set updated_at on insert
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
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
        $revision = new WikiRevision(['scenario' => 'create']);
        $revision->wiki_id = $this->id;
        $revision->setAttributes($this->attributes);
        $revision->memo = $insert ? null : $this->memo;
        $revision->save(false);

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

        //        $content=preg_replace_callback('!<h(2|3)>(.+?)</h\d>!', array($this, 'processHeadings'), $model->htmlContent);
//        if(count($this->headings)>=2 && strlen($content)>5000) // sufficiently long
//        {
//            $toc=array();
//            foreach($this->headings as $heading)
//                $toc[]="<div class=\"ref level-{$heading['level']}\">".l($heading['title'],'#'.$heading['id']).'</div>';
//            $content='<div class="toc">'.implode("\n",$toc)."</div>\n".$content;
//        }

        // TODO replace h tags

        // TODO HTML Purify
        return ApiMarkdown::process($this->content);
    }

    public function getTeaser()
    {
        $teaser = reset(preg_split("/\n\s+\n/", $this->content, -1, PREG_SPLIT_NO_EMPTY));
        $teaser = StringHelper::truncate($teaser, 400);
        return Markdown::process($teaser, 'gfm');
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
        return $this->hasMany(WikiRevision::className(), ['wiki_id' => 'id'])->orderBy(['updated_at' => SORT_DESC]);
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

}
