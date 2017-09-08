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
	];

	public function generateModels()
	{
		return [];
	}

	/**
	 * @return Rating
	 */
	public function generateModel()
	{
		// TODO implement
	}
}
