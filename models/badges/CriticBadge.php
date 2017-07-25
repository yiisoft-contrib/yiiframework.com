<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\Rating;
use app\models\UserBadge;

class CriticBadge extends Badge
{
    public $name = 'Critic';
    public $description = 'First down vote';

    public function earned(UserBadge $badge)
    {
        $sql = 'SELECT created_at FROM {{%rating}} WHERE user_id = :user_id AND rating = 0 ORDER BY created_at ASC LIMIT 1';
        if($date = $this->getDb()->createCommand($sql, [':user_id' => $badge->user_id])->queryScalar())
        {
            $badge->progress = 100;
            $badge->create_time = $date;
            $badge->complete_time = $date;
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
