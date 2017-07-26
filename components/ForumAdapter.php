<?php

namespace app\components;

use yii\base\Component;
use yii\db\Connection;

class ForumAdapter extends Component
{
    public function getReputations($user)
    {
        return [];
        // TODO make this work via oauth or API, forum user may not share ID in new setup
        $sql = 'SELECT rep_date, rep_rating FROM ipb_reputation_index WHERE member_id = :user_id ORDER BY rep_date ASC';
        $cmd = $this->getForumDb()->createCommand($sql, [':user_id' => $user->id]);
        return $cmd->queryAll();
    }

    public function getPostDate($user, $number)
    {
        return false;
        // TODO make this work via oauth or API, forum user may not share ID in new setup
        $n = ((int) $number) - 1;
        $sql = 'SELECT post_date FROM ipb_posts WHERE author_id = :user_id ORDER BY post_date ASC LIMIT '.($n > 0 ? "$n," : '').'1';
        $cmd = $this->getForumDb()->createCommand($sql, [':user_id' => $user->id]);
        return $cmd->queryScalar();
    }

    public function getPostCount($user)
    {
        return 0;
        // TODO make this work via oauth or API, forum user may not share ID in new setup
        $sql = 'SELECT count(*) FROM ipb_posts WHERE author_id = :user_id';
        $cmd = $this->getForumDb()->createCommand($sql, [':user_id' => $user->id]);
        return $cmd->queryScalar();
    }

    /**
     * @return Connection
     */
    private function getForumDb()
    {
        // TODO
    }
}
