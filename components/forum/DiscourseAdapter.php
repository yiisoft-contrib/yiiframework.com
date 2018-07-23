<?php

namespace app\components\forum;

use app\models\User;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\httpclient\Client;

/**
 * DiscourseAdapter implements a forum bridge between the Discourse Forum and the application.
 * Configure as follows:
 *
 * ```php
 * 'forumBridge' => [
 *      'class' => \app\components\forum\DiscourseAdapter::class,
 *      'apiUrl' => 'https://forum.yiiframework.com/',
 *      'apiToken' => '123456',
 *  ],
 * ```
 *
 * @see https://docs.discourse.org/
 */
class DiscourseAdapter extends Component implements ForumAdapterInterface
{
    /**
     * @var string discourse API URL.
     */
    public $apiUrl;
    /**
     * @var string discourse API auth token.
     */
    public $apiToken;


    /**
     * @return Client
     */
    private function getClient()
    {
        $client = new Client([
            'baseUrl' => $this->apiUrl,
        ]);

        return $client;
    }


    public function getPostDate($user, $number)
    {
        if (!$user->forum_id) {
            return false;
        }

        $tablePrefix = $this->tablePrefix;
        $n = ((int) $number) - 1;
        $sql = "SELECT post_date FROM {$tablePrefix}posts WHERE author_id = :user_id ORDER BY post_date ASC LIMIT " . ($n > 0 ? "$n," : '') . '1';
        $cmd = $this->db->createCommand($sql, [':user_id' => $user->forum_id]);
        return $cmd->queryScalar();
    }

    /**
     * @param User $user
     * @return int
     */
    public function getPostCount($user)
    {
        if (!$user->forum_id) {
            $userData = $this->getClient()->get([sprintf('/users/%s.json', $user->username), 'api_key' => $this->apiToken])->send();
            if (isset($userData['user']['id'])) {
                $user->updateAttributes(['forum_id' => $userData['user']['id']]);
            } else {
                return 0;
            }
        }

        $userData = $this->getClient()->get([sprintf('/admin/users/%d.json', $user->forum_id), 'api_key' => $this->apiToken])->send();

        return $userData['user']['post_count'] ?? 0;
    }

    public function getPostCounts()
    {
        // TODO implement
        return [];
//        $tablePrefix = $this->tablePrefix;
//        $sql = "SELECT member_id, posts FROM {$tablePrefix}members";
//        return ArrayHelper::map($this->db->createCommand($sql)->queryAll(),'member_id','posts');
    }

    /**
     * Creates forum user
     *
     * @param User $user
     * @param string $password
     * @return int forum user ID
     */
    public function ensureForumUser(User $user, $password)
    {
        return null;
    }

    public function changeUserPassword(User $user, $password)
    {
    }
}
