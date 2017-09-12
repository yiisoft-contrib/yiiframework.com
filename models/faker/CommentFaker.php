<?php

namespace app\models\faker;
use app\models\Comment;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class CommentFaker extends BaseFaker
{
	public $depends = [
		UserFaker::class,
		WikiFaker::class,
		ExtensionFaker::class,
	];

	/**
	 * @return Comment
	 */
	public function generateModel()
	{
		$modelClass = $this->faker->randomElement([WikiFaker::class, ExtensionFaker::class]);
		$model = $this->faker->randomElement($this->dependencies[$modelClass]);
		$user = $this->faker->randomElement($this->dependencies[UserFaker::class]);

		$comment = new Comment([
			'object_type' => $model::COMMENT_TYPE,
			'object_id' => $model->id,
			'text' => implode("\n\n", $this->faker->paragraphs($this->faker->randomDigit + 1)),
		]);
		$comment->detachBehavior('blameable');
		$comment->user_id = $user->id;
		return $comment;
	}
}
