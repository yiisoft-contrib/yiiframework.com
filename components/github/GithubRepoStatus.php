<?php

namespace app\components\github;

use Github\Api\Repo;
use Github\Client;
use yii\caching\CacheInterface;

class GithubRepoStatus
{
    const COMMIT_CACHE_DURATION = 2592000; // 30 days
    const TAG_CACHE_DURATION = 3600; // 1 hour

    /**
     * @var Repo
     */
    private $repoApi;

    private $cache;

    private $username;

    private $repository;

    public function __construct(CacheInterface $cache, Client $client, $username, $repository)
    {
        $this->repoApi = new Repo($client);
        $this->username = $username;
        $this->repository = $repository;
        $this->cache = $cache;
    }

    private function fetchTagsDesc()
    {
        $releases = $this->repoApi->tags($this->username, $this->repository);

        usort($releases, function ($a, $b) {
            $a = explode('.', $a['name']);
            if (count($a) < 4) {
                $a[] = 0;
            }

            $b = explode('.', $b['name']);
            if (count($b) < 4) {
                $b[] = 0;
            }

            for ($i = 0; $i < 4; $i++) {
                if ($a[$i] > $b[$i]) {
                    return -1;
                }

                if ($a[$i] < $b[$i]) {
                    return 1;
                }
            }

            return 0;
        });

        return $releases;
    }

    private function fetchLatestTag()
    {
        return $this->cache->getOrSet(
            $this->username . '/' . $this->repository . '/latestTag',
            function() {
                $tags = $this->fetchTagsDesc();
                if (isset($tags[0])) {
                    return $tags[0];
                }

                return null;
            },
            self::TAG_CACHE_DURATION
        );
    }

    private function fetchCommit($sha)
    {
        return $this->cache->getOrSet(
            $this->username . '/' . $this->repository . '/' . $sha,
            function () use ($sha) {
                return $this->repoApi->commits()->show($this->username, $this->repository, $sha);
            },
            self::COMMIT_CACHE_DURATION
        );
    }

    public function getInfo()
    {
        $latestTag = $this->fetchLatestTag();
        if ($latestTag === null) {
            return null;
        }

        $tagCommit = $this->fetchCommit($latestTag['commit']['sha']);
        $releaseTag = $latestTag['name'];
        $releaseDate = new \DateTime($tagCommit['commit']['author']['date']);
        $today = new \DateTime();
        $daysSince = $today->diff($releaseDate)->format('%a');

        $diffUrl = "https://github.com/yiisoft/yii2/compare/$releaseTag...master";

        return [
            'repository' => "$this->username/$this->repository",
            'latest' => $releaseTag,
            'no_release_for' => $daysSince,
            'diff' => $diffUrl,
            'status' => "https://img.shields.io/travis/$this->username/$this->repository.svg",
        ];
    }
}