<?php

namespace app\components\github;

use Github\Api\GraphQL;
use Github\Client as GithubClient;
use yii\helpers\ArrayHelper;

class GithubRepoStatus
{
    private GithubClient $client;
    private array $repositories;

    public function __construct(array $repositories, GithubClient $client)
    {
        $this->repositories = $repositories;
        $this->client = $client;
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
                committedDate
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
        if ($repository['refs']['edges'] === null) {
            return [];
        }

        $tags = array_filter(
            ArrayHelper::map(
                $repository['refs']['edges'],
                'node.target.name',
                function ($edge) {
                    $value = ArrayHelper::getValue($edge, 'node.target.target.pushedDate');
                    return $value ?? ArrayHelper::getValue($edge, 'node.target.target.committedDate');
                }
            )
        );

        // Consolidate tags with last release
        $lastRelease = ArrayHelper::getValue($repository, 'releases.edges.0.node', false);
        if ($lastRelease) {
            $tags[$lastRelease['tagName']] = $lastRelease['createdAt'];
        }

        return $tags;
    }

    public function getData()
    {
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
                    'status' => "https://travis-ci.com/{$repository['nameWithOwner']}.svg",
                    'githubStatus' => "https://github.com/{$repository['nameWithOwner']}/workflows/build/badge.svg",
                    'coverage' => "https://scrutinizer-ci.com/g/{$repository['nameWithOwner']}/badges/coverage.png",
                    'quality' => "https://scrutinizer-ci.com/g/{$repository['nameWithOwner']}/badges/quality-score.png",
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
    }
}
