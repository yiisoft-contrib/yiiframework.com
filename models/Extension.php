<?php

namespace app\models;

use app\components\SluggableBehavior;
use dosamigos\taggable\Taggable;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%extension}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $tagline
 * @property integer $category_id
 * @property integer $license_id
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
class Extension extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_PENDING_APPROVAL = 2;
    const STATUS_PUBLISHED = 3;
    const STATUS_DELETED = 5;

    /**
     * object type used for wiki comments
     */
    const COMMENT_TYPE = 'extension';

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
                'createdByAttribute' => 'owner_id', // TODO owner is must have and should not be changed
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
        return '{{%extension}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'tagline', 'category_id', 'license_id', 'owner_id', 'created_at'], 'required'],
            [['category_id', 'license_id', 'owner_id', 'total_votes', 'up_votes', 'featured', 'comment_count', 'download_count', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['rating'], 'number'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 32],
            [['tagline'], 'string', 'max' => 128],
            [['yii_version'], 'string', 'max' => 5],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['owner_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtensionCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
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
     * @inheritdoc
     * @return ExtensionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExtensionQuery(get_called_class());
    }
}
