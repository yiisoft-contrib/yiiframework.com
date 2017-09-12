<?php

namespace app\models\faker;
use app\models\Rating;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class RatingFaker extends BaseFaker
{
	public $depends = [
		UserFaker::class,
		WikiFaker::class,
		ExtensionFaker::class,
		CommentFaker::class,
	];

	public function generateModels()
	{
		$count = $this->faker->numberBetween(5, 15);
		for($i = 0; $i < $count; ++$i) {
			$this->generateModel();
		}
		return [];
	}

	/**
	 * @return Rating
	 */
	public function generateModel()
	{
		$modelClass = $this->faker->randomElement([WikiFaker::class, ExtensionFaker::class, CommentFaker::class]);
		$model = $this->faker->randomElement($this->dependencies[$modelClass]);
		$user = $this->faker->randomElement($this->dependencies[UserFaker::class]);

		Rating::castVote($model, $user->id, mt_rand(0, 100) > 30 ? 1 : 0);
		return null;
	}
}
