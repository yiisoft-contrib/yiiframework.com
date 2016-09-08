<?php
namespace app\models\badges;

use app\models\Badge;


class Reputation3Badge extends Reputation1Badge
{
    public $name = 'Outstanding Reputation';
    public $description = 'Forum Reputation of +40';
    public $threshold = 40;
    public $min = 21;
}

