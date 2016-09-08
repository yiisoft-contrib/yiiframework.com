<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\UserBadge;

class EditorBadge extends Badge
{
    public $name = 'Editor';
    public $description = 'A wiki editor';

    public function earned(UserBadge $badge)
    {
        $sql = sprintf('SELECT update_time FROM tbl_wiki_revision WHERE updater_id = %d ORDER BY update_time ASC LIMIT 1', $badge->user_id);
        if($date = $this->queryScalar($sql))
        {
            // earned when with the latest wiki edit
            $badge->progress = 100;
            $badge->create_time = $date;
            $badge->complete_time = $date;
            return true;
        } 
    }
}
