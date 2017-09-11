<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\Extension;
use app\models\UserBadge;

class ExtensionBadge extends Badge
{
    public $name = 'Extension Smith';
    public $description = 'Contributed 1 Extension';

    public function earned(UserBadge $badge)
    {
        $sql = 'SELECT created_at FROM {{%extension}} WHERE owner_id = :user_id ORDER BY created_at ASC LIMIT 1';
        if ($date = static::getDb()->createCommand($sql, [':user_id' => $badge->user_id])->queryScalar()) {
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
            [Extension::class, Extension::EVENT_AFTER_INSERT],
        ];
    }
}
