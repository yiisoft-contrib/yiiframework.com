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
            while (count($a) < 4) {
                $a[] = 0;
            }

            $b = explode('.', $b['name']);
            while (count($b) < 4) {
                $b[] = 0;
            }

            for ($i = 0; $i < 4; $i++) {
                if (!is_numeric($a[$i])) {
                    $a[$i] = -1;
                }

                if (!is_numeric($b[$i])) {
                    $b[$i] = -1;
                }

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
                return $tags[0] ?? null;
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
        $data = [
            'repository' => "$this->username/$this->repository",
            'latest' => '',
            'no_release_for' => null,
            'diff' => '',
            'status' => "https://img.shields.io/travis/$this->username/$this->repository.svg",
        ];

        $latestTag = $this->fetchLatestTag();
        if ($latestTag === null) {
            return $data;
        }

        $tagCommit = $this->fetchCommit($latestTag['commit']['sha']);
        $releaseTag = $latestTag['name'];
        $releaseDate = new \DateTime($tagCommit['commit']['author']['date']);
        $today = new \DateTime();
        $daysSince = $today->diff($releaseDate)->format('%a');

        $diffUrl = "https://github.com/$this->username/$this->repository/compare/$releaseTag...master";

        $data['latest'] = $releaseTag;
        $data['no_release_for'] = $daysSince;
        $data['diff'] = $diffUrl;

        return $data;
    }
}