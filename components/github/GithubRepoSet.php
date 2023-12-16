<?php

namespace app\components\github;

use DateTime;
use Github\Api\GraphQL;
use Github\Client as GithubClient;
use yii\helpers\ArrayHelper;

class GithubRepoSet
{
    private $client;
    private $repositories;

    private const GRAPHQL_QUERY_REPO_LIMIT = 28;

    public function __construct(array $repositories, GithubClient $client)
    {
        $this->repositories = $repositories;
        $this->client = $client;
    }

    private function getGraphQLQueries()
    {
        $queries = [];
        foreach (array_chunk($this->repositories, self::GRAPHQL_QUERY_REPO_LIMIT) as $repoGroup) {
            foreach ($repoGroup as list($owner, $name)) {
                $alias = $owner . '_' . str_replace('-', '_', $name);
                $query = '{';
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
  repositoryTopics(first: 10) {
    nodes {
      topic {
        name
      }
    }
  }
}
GRAPHQL;
                $query .= '}';
                $queries[] = $query;
            }
        }

        return $queries;
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
        $data = [];
        foreach ($this->getGraphQLQueries() as $query) {
            $results = (new GraphQL($this->client))->execute($query);

            if (isset($results['data'])) {
                foreach ($results['data'] as $repository) {
                    $data[] = $this->getRepositoryData($repository);
                }
            } elseif (isset(
                $results['errors'][0]['message'],
                $results['errors'][0]['locations'][0]['line'],
            )) {
                throw new GithubParseException(
                    $results['errors'][0]['message'],
                    $query,
                    $results['errors'][0]['locations'][0]['line'],
                );
            } elseif (isset($results['errors'][0]['message'])) {
                throw new GithubException($results['errors'][0]['message']);
            } else {
                throw new GithubException('Unable to perform query.');
            }
        }

        return $data;
    }

    private function getRepositoryData($responseData)
    {
        $data = [
            'repository' => $responseData['nameWithOwner'],
            'issues' => $responseData['issues']['totalCount'],
            'pullRequests' => $responseData['pullRequests']['totalCount'],
            'latest' => '',
            'no_release_for' => null,
            'diff' => '',
            'status' => "https://travis-ci.com/{$responseData['nameWithOwner']}.svg",
            'githubStatus' => "https://github.com/{$responseData['nameWithOwner']}/workflows/build/badge.svg",
            'coverage' => "https://scrutinizer-ci.com/g/{$responseData['nameWithOwner']}/badges/coverage.png",
            'quality' => "https://scrutinizer-ci.com/g/{$responseData['nameWithOwner']}/badges/quality-score.png",
            'mergedSinceRelease' => [],
            'optionalForFrameworkAnnounce' => $this->getOptionalForFrameworkAnnounce($responseData),
        ];

        $versions = $this->getVersionsForRepository($responseData);

        if (count($versions)) {
            uksort($versions, 'version_compare');

            $date = end($versions);
            $latest = key($versions);

            $data['latest'] = $latest;

            $latestDate = new DateTime($date);
            $today = new DateTime();

            $data['no_release_for'] = $today->diff($latestDate)->format('%a');

            $data['diff'] = "https://github.com/{$responseData['nameWithOwner']}/compare/$latest...master";

            $mergedSinceRelease = [];
            foreach ($responseData['mergedPRs']['nodes'] as $pr) {
                $mergedAt = new DateTime($pr['mergedAt']);
                if ($mergedAt > $latestDate) {
                    $mergedSinceRelease[] = $pr;
                }
            }

            $data['mergedSinceRelease'] = $mergedSinceRelease;
        }

        return $data;
    }

    private function getOptionalForFrameworkAnnounce($repository)
    {
        foreach ($repository['repositoryTopics']['nodes'] as $topic) {
            if ($topic['topic']['name'] === 'optionalforframeworkannounce') {
                return true;
            }
        }

        return false;
    }
}
