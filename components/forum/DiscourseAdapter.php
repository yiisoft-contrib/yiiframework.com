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
     * @var string discourse API user name for requests that need admin permission.
     */
    public $apiAdminUser = 'cebe';


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
            $response = $this->getClient()->get([$url = sprintf('/users/%s.json', $user->username), 'api_key' => $this->apiToken, 'api_username' => $this->apiAdminUser])->send();
            $userData = $response->data;
            if (isset($userData['user']['id'])) {
                $user->updateAttributes(['forum_id' => $userData['user']['id']]);
            } else {
                Yii::error("Discourse API request returned invalid response: $url");
                Yii::error($response);
                return 0;
            }
        }

        $response = $this->getClient()->get([$url = sprintf('/admin/users/%d.json', $user->forum_id), 'api_key' => $this->apiToken, 'api_username' => $this->apiAdminUser])->send();
        if ($response->statusCode != 200) {
            Yii::error("Discourse API request returned error: $url");
            Yii::error($response);
            return 0;
        }
        $userData = $response->data;
        return $userData['user']['post_count'] ?? 0;
    }

    public function getPostCounts()
    {
        // not implemented for discourse
        return null;
    }

    public function getPostCountsByUsername()
    {
        $postCounts = [];
        $url = '/directory_items.json?period=all&order=post_count&api_key=' . urlencode($this->apiToken) . '&api_username=' . urlencode($this->apiAdminUser);
        while (true) {
            $response = $this->getClient()->get($url)->send();
            if ($response->statusCode != 200) {
                Yii::error("Discourse API request returned error: " . preg_replace('/api_key=.+?&/', 'api_key=...', $url));
                Yii::error($response);
                return $postCounts;
            }
            $userData = $response->data;

            foreach($userData['directory_items'] as $item) {
                $postCounts[$item['user']['username']] = $item['topic_count'] + $item['post_count'];
            }

            if (!isset($userData['load_more_directory_items'])) {
                break;
            }
            // workaround Discourse bug https://meta.discourse.org/t/directory-items-json-api-returns-wrong-link-for-next-page/96268
            $moreItemsUrl = str_replace('?', '.json?', $userData['load_more_directory_items']);
            $url = $moreItemsUrl . '&api_key=' . urlencode($this->apiToken) . '&api_username=' . urlencode($this->apiAdminUser);
        }

        return $postCounts;
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
            $response = $this->getClient()->get(['/admin/badges.json', 'api_key' => $this->apiToken, 'api_username' => $this->apiAdminUser])->send();
            if ($response->statusCode != 200) {
                Yii::error("Discourse API request returned error: /admin/badges.json");
                Yii::error($response);
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
