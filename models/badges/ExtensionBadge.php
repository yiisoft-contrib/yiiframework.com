<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\UserBadge;

class ExtensionBadge extends Badge
{
    public $name = 'Extension Smith';
    public $description = 'Contributed 1 Extension';

    public function earned(UserBadge $badge)
    {
        $sql = sprintf('SELECT create_time FROM tbl_extension WHERE owner_id = %d ORDER BY create_time ASC LIMIT 1', $badge->user_id);
        if($date = $this->queryScalar($sql))
        {
            $badge->progress = 100;
            $badge->create_time = $date;
            $badge->complete_time = $date;
            return true;
        }        
    }
}
