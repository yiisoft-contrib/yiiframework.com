<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\UserBadge;

class SupporterBadge extends Badge
{
    public $name = 'Supporter';
    public $description = 'First up vote';

    public function earned(UserBadge $badge)
    {
        $sql = sprintf('SELECT create_time FROM tbl_rating WHERE user_id = %d AND rating = 1 ORDER BY create_time ASC LIMIT 1', $badge->user_id);
        if($date = $this->queryScalar($sql))
        {
            $badge->progress = 100;
            $badge->create_time = $date;
            $badge->complete_time = $date;
            return true;
        }
    }
}
