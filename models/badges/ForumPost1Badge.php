<?php

namespace app\models\badges;

use app\components\forum\ForumAdapterInterface;
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
        $posts = $this->countPosts($badge->user, $this->threshold);
        if ($posts['count'] >= $this->min && !empty($posts['start'])) {
            $badge->create_time = $posts['start'];
            $requires = $this->threshold - $this->min + 1;
            $current = $posts['count'] - $this->min + 1;
            $badge->progress = round($current / $requires * 100.0);
            if (!empty($posts['complete']))
                $badge->complete_time = $posts['complete'];
            return true;
        }
        return false;
    }

    protected function countPosts($user, $threshold)
    {
        /** @var ForumAdapterInterface $adapter */
        $adapter = \Yii::$app->forumAdapter;
        $start = $adapter->getPostDate($user, 1);
        $complete = $adapter->getPostDate($user, $threshold);
        $count = min($adapter->getPostCount($user), $threshold);
        return array('count' => $count, 'start' => $start, 'complete' => $complete ? $complete : null);
    }
}
