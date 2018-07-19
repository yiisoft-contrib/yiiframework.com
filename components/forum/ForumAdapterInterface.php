<?php
namespace app\components\forum;

use app\models\User;

interface ForumAdapterInterface
{
    public function getPostDate($user, $number);
    public function getPostCount($user);
    public function getPostCounts();
    public function ensureForumUser(User $user, $password);
    public function changeUserPassword(User $user, $password);
}