<?php

namespace app\models\faker;
use app\models\Badge;
use yii\db\ActiveRecord;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class BadgeFaker extends BaseFaker
{
	public $depends = [
		UserFaker::class,
	];

	/**
	 * @return Badge[]
	 */
	public function generateModels()
	{
		if (Badge::find()->count() > 0) {
			return Badge::find()->all();
		}

		$data = [
			['id' => 1,	'urlname' => 'editor', 'class' => 'EditorBadge'],
			['id' => 2,	'urlname' => 'commentator',	'class' => 'CommentatorBadge'],
			['id' => 3,	'urlname' => 'extension-smith', 'class' => 'ExtensionBadge'],
			['id' => 4,	'urlname' => 'supporter', 'class' => 'SupporterBadge'],
			['id' => 5,	'urlname' => 'critic', 'class' => 'CriticBadge'],
			['id' => 6,	'urlname' => 'civic-duty', 'class' => 'CivicDutyBadge'],
			['id' => 7,	'urlname' => 'greenhorn', 'class' => 'ForumPost1Badge'],
			['id' => 8,	'urlname' => 'regular', 'class' => 'ForumPost2Badge'],
			['id' => 9,	'urlname' => 'mogul', 'class' => 'ForumPost3Badge'],
			['id' => 10, 'urlname' => 'super-star', 'class' => 'ForumPost4Badge'],
		];
		foreach($data as $badge) {
			Badge::getDb()->createCommand()->insert(Badge::tableName(), $badge)->execute();
			$this->stdout('.');
		}
		return Badge::find()->all();
	}

	/**
	 * @return ActiveRecord
	 */
	public function generateModel()
	{
	}
}
