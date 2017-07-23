<?php

namespace app\models;

use DiffMatchPatch\Diff;
use DiffMatchPatch\DiffMatchPatch;
use Yii;
use yii\base\InvalidCallException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "wiki_revision".
 *
 * @property integer $wiki_id
 * @property integer $revision
 * @property string $title
 * @property string $content
 * @property integer $category_id
 * @property string $yii_version
 * @property string $memo
 * @property integer $updater_id
 * @property string $updated_at
 *
 * @property User $updater
 * @property Wiki $wiki
 */
class WikiRevision extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => $this->timeStampBehavior()['value'],
                'createdAtAttribute' => false,
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => false,
                'updatedByAttribute' => 'updater_id',
            ],
            // TODO store tags
//            'tagable' => [
//                'class' => Taggable::className(),
//            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wiki_revision}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'category_id', 'memo'], 'required'],
            [['content'], 'string'],
            [['title', 'memo'], 'string', 'max' => 255],
            [['yii_version'], 'string', 'max' => 5],
        ];
    }


    public function scenarios()
    {
        return [
            'create' => ['title', 'content', 'category_id', 'memo', 'yii_version']
        ];
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            throw new InvalidCallException('Updating a Wiki Revision is not allowed!');
        }

        if ($insert && $this->revision === null) {
            $this->revision = (int) static::find()->where(['wiki_id' => $this->wiki_id])->max('revision');
            $this->revision++;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'wiki_id' => 'Wiki ID',
            'revision' => 'Revision',
            'title' => 'Title',
            'content' => 'Content',
            'category_id' => 'Category ID',
            'memo' => 'Memo',
            'updater_id' => 'Updater ID',
            'updated_at' => 'Updated At',
        ];
    }

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
    public function getWiki()
    {
        return $this->hasOne(Wiki::className(), ['id' => 'wiki_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(WikiCategory::className(), ['id' => 'category_id']);
    }

    public function findPrevious()
    {
        return static::find()
            ->where(['wiki_id' => $this->wiki_id])
            ->andWhere(['<', 'revision', $this->revision])
            ->orderBy(['revision' => SORT_DESC])
            ->one();
    }

    public function findNext()
    {
        return static::find()
            ->where(['wiki_id' => $this->wiki_id])
            ->andWhere(['>', 'revision', $this->revision])
            ->orderBy(['revision' => SORT_ASC])
            ->one();
    }

    public static function findLatest($wiki_id)
    {
        return static::find()
            ->where(['wiki_id' => $wiki_id])
            ->orderBy(['revision' => SORT_DESC])
            ->limit(1)->one();
    }

    public function isLatest()
    {
        return $this->equals($this->findLatest($this->wiki_id));
    }

    public static function diff(WikiRevision $a, WikiRevision $b, $attribute)
    {
        $diff = new DiffMatchPatch();
        $diffs = $diff->diff_main(
            (string) ArrayHelper::getValue($a, $attribute, ''),
            (string) ArrayHelper::getValue($b, $attribute, '')
        );
        $diff->diff_cleanupSemantic($diffs);
        return $diffs;
    }

    /**
     * Convert a diff array into a pretty HTML report.
     *
     * @return string HTML representation.
     */
    public static function diffPrettyHtml($diffs)
    {
        $html = '';
        $diffs = array_values($diffs);
        $c = count($diffs);
        for($i = 0; $i < $c; ++$i) {
            $change = $diffs[$i];
            $op = $change[0];
            $data = $change[1];
            $text = str_replace(array(
                '&', '<', '>',
            ), array(
                '&amp;', '&lt;', '&gt;'
            ), $data);

            if ($op == Diff::INSERT) {
                $html .= '<ins>' . nl2br($text) . '</ins>';
            } elseif ($op == Diff::DELETE) {
                $html .= '<del>' . nl2br($text) . '</del>';
            } else {
                $pos = ($i == 0 ? 'first' :
                       (($i == $c - 1) ? 'last' : 'middle'));
                $html .= static::trimContext($text, $pos);
            }
        }

        return $html;
    }

    /**
     * make long unchanged text smaller.
     * @param string $text
     */
    private static function trimContext($text, $pos)
    {
        $threshold = 6;

        $lines = explode("\n", $text);
        $count = count($lines);

        if ($count <= $threshold) {
            return $text;
        }

        switch($pos)
        {
            case "first":
                return '<div class="diff-snip">[...]</div>'
                     . '<span>' . nl2br(ltrim(implode("\n", array_slice($lines, $count - $threshold)))) . '</span>';
            case "last":
                return '<span>' . nl2br(rtrim(implode("\n", array_slice($lines, 0, $threshold)))) . '</span>'
                     . '<div class="diff-snip">[...]</div>';
            default:
                return '<span>' . nl2br(rtrim(implode("\n", array_slice($lines, 0, $threshold / 2)))) . '</span>'
                     . '<div class="diff-snip">[...]</div>'
                     . '<span>' . nl2br(ltrim(implode("\n", array_slice($lines, $count - $threshold / 2)))) . '</span>';
        }
    }

    /**
     * @return array url to this object. Should be something to be passed to [[\yii\helpers\Url::to()]].
     */
    public function getUrl($action = null, $params = [])
    {
        $url = ["wiki/revision", 'id' => $this->wiki_id, 'r1' => $this->revision];
        return empty($params) ? $url : array_merge($url, $params);
    }

}
