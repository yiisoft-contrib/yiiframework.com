<?php

namespace app\models\faker;
use app\models\Wiki;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class WikiRevisionFaker extends BaseFaker
{
	public $depends = [
		UserFaker::class,
		WikiCategoryFaker::class,
		WikiFaker::class,
	];

	/**
	 * @return Wiki
	 */
	public function generateModel()
	{
		$faker = $this->faker;
		/** @var Wiki $wiki */
		$wiki = $faker->randomElement($this->dependencies[WikiFaker::class]);
		$wiki->scenario = 'update';

		$content = explode("\n\n", $wiki->content);

		// add some paragraphs and remove some
		foreach($content as $i => $c) {
			$r = random_int(0, 100);
			if ($r < 30) {
				// remove paragraph
				unset($content[$i]);
			} elseif ($r < 60) {
				// change complete paragraph
				$content[$i] = $faker->paragraph;
			} elseif ($r < 80) {
				// add new paragraph
				$content[$i] .=  "\n\n" . $faker->paragraph;
			} else {
				// typo change
				$words = explode(' ', $content[$i]);
				$words[random_int(0, count($words) - 1)] = $faker->word;
				$content[$i] = implode(' ', $words);
			}
		}
		if (empty($content)) {
			$content = $faker->paragraphs($faker->randomDigit + 2);
		}
		$wiki->content = implode("\n\n", $content);
		$wiki->memo = $faker->sentence(12);

		if (random_int(0, 100) < 30) {
			$wiki->title = ucfirst($faker->sentence(7));
		}
		if (random_int(0, 100) < 20) {
			$wiki->category_id = $faker->randomElement($this->dependencies[WikiCategoryFaker::class])->id;
		}
		if (random_int(0, 100) < 10) {
			$wiki->yii_version = $faker->randomElement(['1.1', '2.0', 'all']);
		}

		$wiki->detachBehavior('blameable');
		$wiki->updater_id = $faker->randomElement($this->dependencies[UserFaker::class])->id;
		$r = random_int(0, 100);
		if ($r > 70) {
			$wiki->tagNames .= ', ' . $faker->word;
		}
		$this->stdout('.');
		return $wiki;
	}
}
