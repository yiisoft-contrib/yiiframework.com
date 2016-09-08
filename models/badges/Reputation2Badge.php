<?php

namespace app\models\badges;

use app\models\Badge;

class Reputation2Badge extends Reputation1Badge
{
    public $name = 'Excellent Reputation';
    public $description = 'Forum Reputation of +20';
    public $threshold = 20;
    public $min = 11;
}

