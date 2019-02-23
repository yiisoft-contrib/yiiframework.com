<?php

namespace app\models\faker;
use app\models\News;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class NewsFaker extends BaseFaker
{
	public $depends = [
		UserFaker::class,
	];

	/**
	 * @return News
	 */
	public function generateModel()
	{
		$faker = $this->faker;
		$news = new News();
		$news->setAttributes([
			'title' => ucfirst($faker->sentence(10)),
			'news_date' => $faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
			'content' => implode("\n\n", $faker->paragraphs($faker->randomDigit + 1)),
			'status' => $faker->randomElement(array_keys(News::getStatusList())),
			'tagNames' => implode(', ', $faker->words($faker->randomDigit)),
		]);
		$news->detachBehavior('blameable');
		$news->updater_id = $news->creator_id = $faker->randomElement($this->dependencies[UserFaker::class])->id;
		$r = random_int(0, 100);
		if ($r > 80) {
			$news->tagNames .= ', Yii 2.0';
		} else if ($r > 50) {
			$news->tagNames .= ', PHP 7';
		}
		$this->stdout('.');
		return $news;
	}
}
