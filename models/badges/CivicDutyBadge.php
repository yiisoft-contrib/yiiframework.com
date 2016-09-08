<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\UserBadge;

class CivicDutyBadge extends Badge
{
    public $name = 'Civic Duty';
    public $description = 'Voted 10 or more times';

    public function earned(UserBadge $badge)
    {
        $sql = sprintf('SELECT create_time FROM tbl_rating WHERE user_id = %d ORDER BY create_time ASC LIMIT 10', $badge->user_id);
        $time = $this->queryColumn($sql);
        $count = count($time);
        if($count > 0)
        {
            $badge->create_time = $time[0];
            $badge->progress = min(100,  $count * 10);
            if($count === 10)
                $badge->complete_time = $time[9];
            return true;
        } 
    }

} 
