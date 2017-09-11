<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\Comment;
use app\models\UserBadge;

class CommentatorBadge extends Badge
{
    public $name = 'Commentator';
    public $description = 'Leaves 10 comments';

    public function earned(UserBadge $badge)
    {
        $sql = 'SELECT created_at FROM {{%COMMENT}} WHERE user_id = :user_id ORDER BY created_at ASC LIMIT 10';
        $time = static::getDb()->createCommand($sql, ['user_id' => $badge->user_id])->queryColumn();
        $count = count($time);
        if ($count > 0) {
            $badge->create_time = $time[0];
            $badge->progress = min(100, $count * 10);
            if ($count === 10)
                $badge->complete_time = $time[9];
            return true;
        }
        return false;
    }

    public static function updateEvents()
    {
        return [
            [Comment::class, Comment::EVENT_AFTER_INSERT],
        ];
    }
}
