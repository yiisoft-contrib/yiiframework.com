<?php

namespace app\components\github;

use Github\Api\GraphQL;
use Github\Client;
use yii\caching\CacheInterface;
use yii\helpers\ArrayHelper;

class GithubRepoStatus
{
    const RELEASES_CACHE_DURATION = 3600; // 1 hour

    private $cache;
    private $client;
    private $repositories;
    private $version;

    public function __construct(CacheInterface $cache, Client $client, $repositories, $version)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->repositories = $repositories;
        $this->version = $version;
    }

    private function getGraphQLQuery()
    {
        $query = '{';
        foreach ($this->repositories as list($owner, $name)) {

            $alias = $owner . '_' . str_replace('-', '_', $name);

            $query .= <<<GRAPHQL

$alias: repository(owner: "$owner", name: "$name") {
  nameWithOwner
  issues(states:OPEN) {
    totalCount
  }
  pullRequests(states:OPEN) {
    totalCount
  }
  mergedPRs: pullRequests(states:MERGED, last: 10) {
    nodes {
      number
      title
      mergedAt
    }
  }
  refs(refPrefix: "refs/tags/", last: 5) {
    edges {
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
            'graphql/repositories-statuses/' . $this->version,
            function () {
                $query = $this->getGraphQLQuery();
                $results = (new GraphQL($this->client))->execute($query);

                $data = [];
                if (isset($results['data'])) {
                    foreach ($results['data'] as $repository) {
                        $datum = [
                            'repository' => $repository['nameWithOwner'],
                            'issues' => $repository['issues']['totalCount'],
                            'pullRequests' => $repository['pullRequests']['totalCount'],
                            'latest' => '',
                            'no_release_for' => null,
                            'diff' => '',
                            'status' => "https://img.shields.io/travis/{$repository['nameWithOwner']}.svg",
                            'mergedSinceRelease' => [],
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

                            $mergedSinceRelease = [];
                            foreach ($repository['mergedPRs']['nodes'] as $pr) {
                                $mergedAt = new \DateTime($pr['mergedAt']);
                                if ($mergedAt > $latestDate) {
                                    $mergedSinceRelease[] = $pr;
                                }
                            }

                            $datum['mergedSinceRelease'] = $mergedSinceRelease;

                        }


                        $data[] = $datum;
                    }
                } elseif (isset(
                    $results['errors'][0]['message'],
                    $results['errors'][0]['locations'][0]['line']
                )) {
                    throw new GithubParseException(
                        $results['errors'][0]['message'],
                        $query,
                        $results['errors'][0]['locations'][0]['line']
                    );
                } elseif (isset($results['errors'][0]['message'])) {
                    throw new GithubException($results['errors'][0]['message']);
                } else {
                    throw new GithubException('Unable to perform query.');
                }

                return $data;
            },
            self::RELEASES_CACHE_DURATION
        );
    }

}
