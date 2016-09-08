<?php

namespace app\models\badges;

use app\models\Badge;

class ForumPost2Badge extends ForumPost1Badge
{
    public $name = 'Forum Regular';
    public $description = '100 Active Forum Posts';
    public $threshold = 100;
    public $min = 26;
}
