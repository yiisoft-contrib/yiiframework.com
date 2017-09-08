<?php

namespace app\models\faker;
use app\models\Wiki;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class WikiFaker extends BaseFaker
{
	public $depends = [
		UserFaker::class,
		WikiCategoryFaker::class,
	];

	/**
	 * @return Wiki
	 */
	public function generateModel()
	{
		$faker = $this->faker;
		$wiki = new Wiki(['scenario' => 'create']);

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

		$wiki->setAttributes([
			'title' => ucfirst($faker->sentence(7)),
			'category_id' => $faker->randomElement($this->dependencies[WikiCategoryFaker::class])->id,
			'yii_version' => $faker->randomElement(['1.1', '2.0', 'all']),
			'content' => implode("\n\n", $content),

			'created_at' => $faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
			'tagNames' => implode(', ', $faker->words($faker->randomDigit)),
		]);
		$wiki->detachBehavior('blameable');
		$wiki->creator_id = $faker->randomElement($this->dependencies[UserFaker::class])->id;
		$r = rand(0, 100);
		if ($r > 80) {
			$wiki->tagNames .= ', Yii 2.0';
		} else if ($r > 50) {
			$wiki->tagNames .= ', PHP 7';
		}
		$this->stdout('.');
		return $wiki;
	}
}
