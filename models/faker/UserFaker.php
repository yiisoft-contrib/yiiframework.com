<?php

namespace app\models\faker;
use app\models\User;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class UserFaker extends BaseFaker
{
	protected $password = 'IloveYii<3';
	/**
	 * @return User
	 */
	public function generateModel()
	{
		$user = new User();
		$user->username = $this->faker->userName;
		$user->password = $this->password;
		$user->email = $this->faker->email;
		$user->display_name = $this->faker->name;

		$this->stdout("\n  generated user: $user->username  $user->email");
		return $user;
	}

	public function generateModels()
	{
		$models = parent::generateModels();
		$this->stdout("\n\nyou may log in with all users using the password: $this->password\n");
		return $models;
	}
}
