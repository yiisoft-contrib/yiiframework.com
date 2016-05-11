<?php

use yii\rbac\Item;

return [
	'news:pAdmin' => [
		'type' => Item::TYPE_PERMISSION,
		'description' => 'Create and Update News entries.',
	],
	'news:Admin' => [
		'type' => Item::TYPE_ROLE,
		'description' => 'Administrator for News section.',
		'children' => [
			'news:pAdmin',
		],
	],
];
