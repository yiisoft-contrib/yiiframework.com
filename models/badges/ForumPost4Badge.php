<?php

namespace app\models\badges;

use app\models\Badge;

class ForumPost4Badge extends ForumPost1Badge
{
    public $name = 'Super Star';
    public $description = '1500 Active Forum Posts';
    public $threshold = 1500;
    public $min = 501;
}
