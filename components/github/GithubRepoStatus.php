<?php

namespace app\components\github;

use Github\Client;
use yii\caching\CacheInterface;
use yii\helpers\ArrayHelper;

class GithubRepoStatus
{
    const RELEASES_CACHE_DURATION = 3600; // 1 hour

    private $cache;
    private $client;
    private $repositories;

    public function __construct(CacheInterface $cache, Client $client, $repositories)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->repositories = $repositories;
    }

    private function getGraphQLQuery()
    {
        $query = '{';
        foreach ($this->repositories as list($owner, $name)) {

            $alias = $owner . '_' . str_replace('-', '_', $name);

            $query .= <<<GRAPHQL
$alias: repository(owner: "$owner", name: "$name") {
  nameWithOwner
  refs(refPrefix: "refs/tags/", last: 5) {
    edges() {
      node {
        target {
          ... on Tag {
            name
            target {
              ... on Commit {
                pushedDate
              }
            }
          }
        }
      }
    }
  }
  releases(first: 1, orderBy: {field: CREATED_AT, direction: DESC}) {
    edges {
      node {
        tagName
        createdAt
      }
    }
  }
}
GRAPHQL;

        }
        $query .= '}';

        return $query;
    }

    /**
     * Extracts tags & releases from a GraphQL repository result set, and returns an array
     * of `version` => `date` entries.
     *
     * @param $repository
     * @return array
     */
    private function getVersionsForRepository($repository) {
        $tags = array_filter(ArrayHelper::map(
            $repository['refs']['edges'],
            'node.target.name',
            'node.target.target.pushedDate'
        ));

        // Consolidate tags with last release
        $lastRelease = ArrayHelper::getValue($repository, 'releases.edges.0.node', false);
        if ($lastRelease) {
            $tags[$lastRelease['tagName']] = $lastRelease['createdAt'];
        }

        return $tags;
    }

    public function getData()
    {
        return $this->cache->getOrSet(
            'graphql/repositories-statuses',
            function () {
                $results = $this->client->api('graphql')->execute($this->getGraphQLQuery());

                $data = [];
                foreach ($results['data'] as $repository) {
                    $datum = [
                        'repository' => $repository['nameWithOwner'],
                        'latest' => '',
                        'no_release_for' => null,
                        'diff' => '',
                        'status' => "https://img.shields.io/travis/{$repository['nameWithOwner']}.svg",
                    ];

                    $versions = $this->getVersionsForRepository($repository);

                    if (count($versions)) {
                        uksort($versions, 'version_compare');

                        $date = end($versions);
                        $latest = key($versions);

                        $datum['latest'] = $latest;

                        $latestDate = new \DateTime($date);
                        $today = new \DateTime();

                        $datum['no_release_for'] = $today->diff($latestDate)->format('%a');

                        $datum['diff'] = "https://github.com/{$repository['nameWithOwner']}/compare/$latest...master";
                    }

                    $data[] = $datum;
                }

                return $data;
            },
            self::RELEASES_CACHE_DURATION
        );
    }

}