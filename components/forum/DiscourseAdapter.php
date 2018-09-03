<?php

namespace app\components\forum;

use app\models\User;
use Yii;
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
        return 0;
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
        // not implemented for discourse
        return null;
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
        // forum users are created via SSO
        return null;
    }

    public function changeUserPassword(User $user, $password)
    {
        // forum users are created via SSO, there is no password in discourse
    }

    /**
     * List of badges provided by the forum
     * @return array
     */
    public function getForumBadges()
    {
        $badges = Yii::$app->cache->get('discourse_badges');
        if ($badges === false) {
            $response = $this->getClient()->get(['/admin/badges.json', 'api_key' => $this->apiToken, 'api_username' => 'cebe'])->send();
            if ($response->statusCode != 200) {
                return [];
            }
            $badges = $response->data['badges'] ?? [];
            foreach($badges as $b => $badge) {
                if (!$badge['enabled']) {
                    unset($badges[$b]);
                    continue;
                }
                // make relative URLs absolute in badge description
                $badges[$b]['description'] = preg_replace('~<a href="/([^"]+)"~', '<a href="'.rtrim($this->apiUrl,'/').'/\1"', $badge['description']);
                $badges[$b]['url'] = rtrim($this->apiUrl,'/').'/badges/' . $badge['id'] . '/' . $badge['slug'];
            }
            Yii::$app->cache->set('discourse_badges', $badges, 1800);
        }
        return $badges;
    }
}
