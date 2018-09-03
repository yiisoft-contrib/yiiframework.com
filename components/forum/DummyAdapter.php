<?php

namespace app\components\forum;

use app\models\User;

/**
 * DummyAdapter is a forum adapter that does nothing.
 * Allows developing without having a forum deployed locally.
 *
 * Configure as follows in your params-local.php:
 *
 * 'forumBridge' => [
 *      'class' => \app\components\forum\DummyAdapter::class,
 *  ],
 */
class DummyAdapter implements ForumAdapterInterface
{

    public function getPostDate($user, $number)
    {
        return false;
    }

    public function getPostCount($user)
    {
        return 0;
    }

    public function getPostCounts()
    {
        return [];
    }

    public function ensureForumUser(User $user, $password)
    {
        return null;
    }

    public function changeUserPassword(User $user, $password)
    {
        // do nothing
    }

    public function getForumBadges()
    {
        return [];
    }
}