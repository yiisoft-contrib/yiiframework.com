<?php

namespace app\models\faker;
use app\jobs\ExtensionImportJob;
use app\models\Extension;
use Yii;
use yii\helpers\Json;
use yii\queue\Queue;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class ExtensionFaker extends BaseFaker
{
	public $depends = [
		UserFaker::class,
		ExtensionCategoryFaker::class,
	];

	/**
	 * @return Extension
	 */
	public function generateModel()
	{
		$faker = $this->faker;
		$extension = new Extension();
		$extension->initDefaults();
		$extension->scenario = $faker->randomElement(['create_packagist', 'create_custom']);

		switch($extension->scenario) {
			case 'create_packagist':
				$extension->from_packagist = 1;
				$extension->setAttributes([
					'packagist_url' => $this->getFreePackagistName(),
					'category_id' => $faker->randomElement($this->dependencies[ExtensionCategoryFaker::class])->id,
					'tagNames' => implode(', ', $faker->words($faker->randomDigit)),
				]);
				$extension->validate();
				$extension->populatePackagistName();
				$extension->description = null;
			    $extension->license_id = null;
				break;
			case 'create_custom':
				$extension->from_packagist = 0;
				$content = $faker->paragraphs($faker->randomDigit + 2);
				// add some code elements and headlines
				foreach($content as $i => $c) {
					$r = rand(0, 100);
					if ($r < 50) {
						$content[$i] = str_repeat("#", rand(1, 4)) . ' ' . ucfirst($faker->sentence(3)) . "\n\n" . $content[$i];
					} elseif ($r < 70) {
						$content[$i] = "```php\n" . $content[$i] . "\n```\n";
					}
				}

				$extension->setAttributes([
					'name' => $this->extensionName(),
					'category_id' => $faker->randomElement($this->dependencies[ExtensionCategoryFaker::class])->id,
					'yii_version' => $faker->randomElement(['1.1', '2.0', 'all']),
					'license_id' => $faker->randomElement(array_keys(Extension::getLicenseSelect())),
					'tagNames' => implode(', ', $faker->words($faker->randomDigit)),
					'tagline' => $faker->sentence(8),
					'description' => implode("\n\n", $content),
				]);
				break;
		}

		$extension->detachBehavior('blameable');
		$extension->owner_id = $faker->randomElement($this->dependencies[UserFaker::class])->id;
		$r = rand(0, 100);
		if ($r > 80) {
			$extension->tagNames .= ', Yii 2.0';
		} else if ($r > 50) {
			$extension->tagNames .= ', PHP 7';
		}
		$this->stdout('.');
		return $extension;
	}

	public function generateModels()
	{
		$models = parent::generateModels();

		/** @var $queue Queue */
		$queue = Yii::$app->queue;

		foreach($models as $model) {
			if ($model->from_packagist) {
				$queue->push(new ExtensionImportJob(['extensionId' => $model->id]));
			}
		}
		return $models;
	}

	private function extensionName($len = 3)
	{
		do {
			$word = $this->faker->word;
		} while (strlen($word) < $len || Extension::find()->andWhere(['name' => $word])->exists());
		return $word;
	}

	private function getFreePackagistName()
	{
		$existingNames = array_flip(Extension::find()->select('packagist_url')->andWhere(['from_packagist' => 1])->column());
		do {
			$name = $this->faker->randomElement($this->getPackagistNames());
		} while(isset($existingNames[Extension::normalizePackagistUrl($name)]));
		return $name;
	}

	private $_packagistNames;

	protected function getPackagistNames()
	{
		if ($this->_packagistNames === null) {
			$data = Json::decode(file_get_contents('https://packagist.org/packages/list.json?type=yii2-extension'));
			$this->_packagistNames = $data['packageNames'];
		}
		return $this->_packagistNames;
	}
}
