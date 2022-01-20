<?php

namespace app\components\github;

use Github\Client as GithubClient;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;

class GithubProgress
{
    const VERSIONS = ['1.1', '2.0', '3.0'];

    private $client;
    private $version;

    /**
     * @var array|null Repositories for all versions.
     */
    private static $allResponseRepositories = null;

    public function __construct(string $version, GithubClient $client)
    {
        if (!in_array($version, static::VERSIONS, true)) {
            $validVersionsStr = implode(',', static::VERSIONS);

            throw new InvalidArgumentException("Invalid version. Valid versions are: $validVersionsStr.");
        }
        $this->version = $version;

        $this->client = $client;
    }

    public function getData(): array
    {
        $repositories = $this->getRepositories();

        $tokenFile = Yii::getAlias('@app/data') . '/github.token';
        if (!file_exists($tokenFile)) {
            throw new InvalidConfigException("Github token is missing. It must be located in $tokenFile.");
        }

        $token = trim(file_get_contents($tokenFile));

        $this->client->authenticate($token, null, GithubClient::AUTH_HTTP_TOKEN);
        $githubRepoStatus = new GithubRepoSet($repositories, $this->client);

        return $githubRepoStatus->getData();
    }

    private function getAllResponseRepositories(): array
    {
        if (static::$allResponseRepositories !== null) {
            return static::$allResponseRepositories;
        }

        $repositories = [];
        $i = 1;

        while (true) {
            echo "Getting organization repositories, page $i...\n";

            $response = $this->client->getHttpClient()->get(
                "/orgs/yiisoft/repos?page=$i&per_page=100",
                ['Accept' => 'application/vnd.github.mercy-preview+json']
            );

            echo "Received organization repositories, page $i.\n";

            $responseRepositories = json_decode($response->getBody()->getContents());
            if (!$responseRepositories) {
                break;
            }

            foreach ($responseRepositories as $responseRepository) {
                if (!$responseRepository->archived) {
                    $repositories[] = $responseRepository;
                }
            }

            $i++;
        }

        sort($repositories);
        static::$allResponseRepositories = $repositories;

        return static::$allResponseRepositories;
    }

    private function getRepositories(): array
    {
        $repositories = [];
        foreach (static::getAllResponseRepositories() as $responseRepository) {
            if (in_array('yii' . (int) $this->version, $responseRepository->topics, true)) {
                $repositories[] = explode('/', $responseRepository->full_name);
            }
        }

        return $repositories;
    }
}
