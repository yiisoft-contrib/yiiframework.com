<?php

namespace app\models\faker;
use app\models\Star;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class StarFaker extends BaseFaker
{
	public $depends = [
		UserFaker::class,
		WikiFaker::class,
		ExtensionFaker::class,
	];

	/**
	 * @return Star
	 */
	public function generateModels()
	{
		$count = $this->faker->numberBetween(5, 15);
		for($i = 0; $i < $count; ++$i) {
			$this->generateModel();
		}
		return [];
	}


	/**
	 * @return null
	 */
	public function generateModel()
	{
		$modelClass = $this->faker->randomElement([WikiFaker::class, ExtensionFaker::class]);
		$model = $this->faker->randomElement($this->dependencies[$modelClass]);
		$user = $this->faker->randomElement($this->dependencies[UserFaker::class]);

		Star::castStar($model, $user->id, 1);
	}
}
