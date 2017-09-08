<?php

namespace app\models\faker;
use app\models\ExtensionCategory;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class ExtensionCategoryFaker extends BaseFaker
{
	private $i = 0;

	/**
	 * @return ExtensionCategory
	 */
	public function generateModel()
	{
		$faker = $this->faker;
		$model = new ExtensionCategory();
		$model->name = ucfirst($faker->word);
		$model->sequence = $this->i++;;
		$this->stdout('.');
		return $model;
	}

	public function generateModels()
	{
		// do not generate models if we have already 10 categories
		if (ExtensionCategory::find()->count() > 10) {
			return ExtensionCategory::find()->all();
		}
		return parent::generateModels();
	}
}
