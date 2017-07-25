<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\Rating;
use app\models\UserBadge;

class CivicDutyBadge extends Badge
{
    public $name = 'Civic Duty';
    public $description = 'Voted 10 or more times';

    public function earned(UserBadge $badge)
    {
        $sql = 'SELECT created_at FROM {{%rating}} WHERE user_id = :user_id ORDER BY created_at ASC LIMIT 10';
        $time = $this->getDb()->createCommand($sql, [':user_id' => $badge->user_id])->queryColumn();
        $count = count($time);
        if($count > 0)
        {
            $badge->create_time = $time[0];
            $badge->progress = min(100,  $count * 10);
            if($count === 10)
                $badge->complete_time = $time[9];
            return true;
        }
        return false;
    }

    public static function updateEvents()
    {
        return [
            [Rating::class, Rating::EVENT_AFTER_INSERT],
        ];
    }
} 
