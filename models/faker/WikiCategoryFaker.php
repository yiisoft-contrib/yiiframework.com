<?php

namespace app\models\faker;
use app\models\WikiCategory;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class WikiCategoryFaker extends BaseFaker
{
	private $i = 0;

	/**
	 * @return WikiCategory
	 */
	public function generateModel()
	{
		$faker = $this->faker;
		$model = new WikiCategory();
		$model->name = ucfirst($faker->word);
		$model->sequence = $this->i++;;
		$this->stdout('.');
		return $model;
	}

	public function generateModels()
	{
		// do not generate models if we have already 10 categories
		if (WikiCategory::find()->count() > 10) {
			return WikiCategory::find()->all();
		}
		return parent::generateModels();
	}
}
