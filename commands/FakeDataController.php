<?php
/**
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\commands;


use app\models\News;
use Faker\Factory;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Populates the database with dummy content for testing
 */
class FakeDataController extends Controller
{
	public $defaultAction = 'populate';

	public function actionPopulate()
	{
		if (!YII_DEBUG) {
			$this->stdout('You should only do this in a development environment!', Console::BOLD, Console::FG_RED);
			return 1;
		}
		if (!$this->confirm('Populate database with fake data?')) {
			return 1;
		}

		$this->stdout('Populating news...');
		$this->populateNews();
		$this->stdout("done.\n", Console::FG_GREEN, Console::BOLD);

		return 0;
	}

	private function populateNews()
	{
		$faker = $this->getFaker();
		for($n = 0; $n < 20; $n++) {
			$news = new News();
			$news->setAttributes([
				'title' => ucfirst($faker->sentence(10)),
				'news_date' => $faker->date(),
				'content' => implode("\n\n", $faker->paragraphs($faker->randomDigit)),
				'status' => $faker->randomElement(array_keys(News::getStatusList())),
			]);
			$news->save(false);
			$this->stdout('.');
		}
	}

	protected function getFaker()
	{
		return Factory::create('en');
	}


}