<?php

namespace app\models\badges;

use app\models\Badge;
use app\models\UserBadge;

class ForumPost1Badge extends Badge
{
    public $name = 'Greenhorn';
    public $description = '25 Active Forum Posts';
    public $threshold = 25;
    public $min = 1;

    public function earned(UserBadge $badge)
    {
        $posts = $this->countPosts($badge->user_id, $this->threshold);
        if($posts['count']>= $this->min && !empty($posts['start']))
        {
            $badge->create_time = $posts['start'];
            $requires = $this->threshold - $this->min + 1;
            $current = $posts['count'] - $this->min + 1;
            $badge->progress = round($current/$requires*100.0);
            if(!empty($posts['complete']))
                $badge->complete_time = $posts['complete'];
            return true;
        }
    }

    protected function countPosts($user, $threshold)
    {
        $db = $this->getForumDb();
        $sql = sprintf('SELECT post_date FROM ipb_posts WHERE author_id = %d ORDER BY post_date ASC LIMIT 1', $user);
        $start = $db->createCommand($sql)->queryScalar();

        $sql = sprintf('SELECT post_date FROM ipb_posts WHERE author_id = %d ORDER BY post_date ASC LIMIT %d,1', $user, $threshold-1);        
        $complete = $db->createCommand($sql)->queryScalar();

        $sql = sprintf('SELECT count(*) FROM ipb_posts WHERE author_id = %d', $user);
        $count = min($db->createCommand($sql)->queryScalar(), $threshold);
        return array('count' => $count, 'start' => $start, 'complete' => $complete ? $complete : null);
    }
}
