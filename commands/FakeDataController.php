<?php

namespace app\commands;

use app\models\faker\BaseFaker;
use app\models\News;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 * Populates the database with dummy content for testing
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class FakeDataController extends Controller
{
	public $defaultAction = 'populate';

	public $fakerPath = '@app/models/faker';
	public $fakerNamespace = 'app\models\faker';


	public function options($actionID)
	{
		return array_merge(parent::options($actionID), ['fakerPath', 'fakerNamespace']);
	}

	/**
	 * Populate the database with dummy content.
	 */
	public function actionPopulate()
	{
		if (!YII_DEBUG || YII_ENV_PROD) {
			$this->stdout('You should only do this in a development environment!', Console::BOLD, Console::FG_RED);
			return 1;
		}
		if (!$this->confirm('Populate database with fake data?')) {
			return 1;
		}

		$fakers = $this->loadFakers();
		$this->runFakers($fakers);

		return 0;
	}

	protected function loadFakers()
	{
		$files = FileHelper::findFiles(Yii::getAlias($this->fakerPath), [
			'recursive' => false,
			'only' => ['*Faker.php'],
			'except' => ['BaseFaker.php'],
		]);
		$fakers = [];
		foreach($files as $file) {
			$class = "$this->fakerNamespace\\" . basename($file, '.php');
			$fakers[$class] = new $class(['faker' => $this->getFaker(), 'controller' => $this]);
		}
		return $fakers;
	}

	/**
	 * Apply fakers considering
	 * @param BaseFaker[] $fakers
	 */
	protected function runFakers($fakers)
	{
		$this->_applied = [];
		foreach($fakers as $faker) {
			$this->applyFaker($faker, $fakers);
		}
	}

	private $_applied = [];

	/**
	 * @param BaseFaker $faker
	 */
	protected function applyFaker($faker, $fakers)
	{
		$class = get_class($faker);
		if (isset($this->_applied[$class])) {
			if ($this->_applied[$class] === false) {
				throw new Exception('Circular dependency detected between Fakers.');
			} else {
				return $this->_applied[$class];
			}
		}
		$this->_applied[$class] = false;

		foreach($faker->depends as $dependency) {
			$models = $this->applyFaker($fakers[$dependency], $fakers);
			$faker->dependencies[$dependency] = $models;
		}
		$this->stdout("Generating models for $class...");
		$models = $faker->generateModels();
		$this->_applied[$class] = $models;
		$this->stdout("done.\n", Console::FG_GREEN, Console::BOLD);
		return $models;
	}

	private $_faker;

	protected function getFaker()
	{
		if ($this->_faker === null) {
			$this->_faker = \Faker\Factory::create('en');
		}
		return $this->_faker;
	}


}