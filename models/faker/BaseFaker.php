<?php

namespace app\models\faker;

use Faker;
use yii\base\Component;
use yii\base\Exception;
use yii\console\Controller;
use yii\db\ActiveRecord;

if (!YII_DEBUG || YII_ENV_PROD) {
	echo "I dare you! Don't use this in production!\n";
	die();
}

/**
 * Base class of fake data generators.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
abstract class BaseFaker extends Component
{
	/**
	 * @var array fakers this faker depends on.
	 */
	public $depends = [];
	/**
	 * @var array dependent models populated when generating.
	 */
	public $dependencies = [];
	/**
	 * @var Controller|null
	 */
	public $controller;
	/**
	 * @var Faker\Generator
	 */
	public $faker;

	/**
	 * @var Faker\UniqueGenerator|Faker\Generator
	 */
	protected $uniqueFaker;


	public function init()
	{
		if ($this->faker === null) {
			$this->faker = Faker\Factory::create('en_GB');
		}
		$this->uniqueFaker = new Faker\UniqueGenerator($this->faker, 100);
		parent::init();
	}

	public function generateModels()
	{
		$count = $this->faker->numberBetween(5, 15);
		$models = [];
		for($i = 0; $i < $count; ++$i) {
			$model = $this->generateModel();
			$this->saveModel($model);
			$models[] = $model;
		}
		return $models;
	}

	/**
	 * @return ActiveRecord
	 */
	abstract public function generateModel();

    /**
     * @param ActiveRecord $model
     * @throws Exception
     */
	protected function saveModel($model)
	{
		if (!$model->save()) {
			throw new Exception('Failed to validate ' . get_class($model) . ' model: ' . print_r($model->getErrors(), true) . print_r($model->getAttributes(), true));
		}
	}

	public function stdout($string)
	{
		$args = func_get_args();
		if ($this->controller === null) {
			echo array_shift($args);
		} else {
			call_user_func_array([$this->controller, 'stdout'], $args);
		}
	}
}
