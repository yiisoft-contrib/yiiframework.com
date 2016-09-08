<?php

namespace app\models\badges;

use app\models\Badge;

class ForumPost3Badge extends ForumPost1Badge
{
    public $name = 'Forum Mogul';
    public $description = '500 Active Forum Posts';
    public $threshold = 500;
    public $min = 101;
}
