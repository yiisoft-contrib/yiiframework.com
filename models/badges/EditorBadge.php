<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\UserBadge;
use app\models\Wiki;

class EditorBadge extends Badge
{
    public $name = 'Editor';
    public $description = 'A wiki editor';

    public function earned(UserBadge $badge)
    {
        $sql = 'SELECT updated_at FROM {{%wiki_revision}} WHERE updater_id = :user_id ORDER BY updated_at ASC LIMIT 1';
        if($date = $this->getDb()->createCommand($sql, [':user_id' => $badge->user_id])->queryScalar())
        {
            // earned when with the latest wiki edit
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
            [Wiki::class, Wiki::EVENT_AFTER_UPDATE],
        ];
    }
}
