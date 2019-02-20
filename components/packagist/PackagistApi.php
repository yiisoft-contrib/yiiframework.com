<?php

namespace app\components\packagist;

use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

/**
 * Package manager for yii2-extension packagist.org packages
 *
 * @see https://packagist.org
 */
class PackagistApi
{
    const ENDPOINT_SEARCH = 'https://packagist.org/search.json?%s';
    const ENDPOINT_LIST = 'https://packagist.org/packages/list.json?%s';
    const ENDPOINT_PACKAGE = 'https://packagist.org/packages/%s/%s.json';

    // TODO make this work when default branch is not master
    const ENDPOINT_GITHUB_RAW_FILE = 'https://raw.githubusercontent.com/%s/master/%s';
    const ENDPOINT_GITLAB_RAW_FILE = 'https://gitlab.com/%s/raw/master/%s';

    /**
     * Get a list of packages
     *
     * @param string $query query
     * @param null|integer $page page
     * @param null|array $sort sorting
     *
     * @return array
     */
    public function search($query, $page = null, $sort = null)
    {
        $packages = [];
        $totalCount = 0;
        $currentPageCount = 0;
        $errorMessage = null;

        $orderBys = [
            [
                'sort' => 'downloads',
                'order' => 'desc',
            ]
        ];
        if ($sort !== null) {
            $orderBys = [];
            foreach ($sort as $kSort => $vSort) {
                $orderBys[] = [
                    'sort' => $kSort,
                    'order' => SORT_ASC === $vSort ? 'asc' : 'desc'
                ];
            }
        }
        $queryParam = [
            'type' => 'yii2-extension',
            'q' => $query,
            'orderBys' => $orderBys
        ];

        if ($page !== null) {
            $queryParam['page'] = $page;
        }

        $url = sprintf(self::ENDPOINT_SEARCH, http_build_query($queryParam));
        $data = $this->fetch($url);

        if (!\is_array($data) || array_diff(['results', 'total'], array_keys($data))) {
            $errorMessage = 'Error getting data from packagist.org';
        } else {
            $currentPageCount = \count($data['results']);
            foreach ($data['results'] as $result) {
                $package = Package::createFromAPIData($result);
                if ($package instanceof Package) {
                    $packages[] = Package::createFromAPIData($result);
                }
            }
            $totalCount = $data['total'];
        }

        return [
            'packages' => $packages,
            'totalCount' => $totalCount,
            'currentPageCount' => $currentPageCount,
            'errorMessage' => $errorMessage
        ];
    }

    /**
     * @param string|null $type e.g. 'yii2-extension'
     * @return array
     * @throws PackagistException
     */
    public function listPackageNames($type = null)
    {
        $queryParam = [
            'type' => $type,
        ];

        $url = sprintf(self::ENDPOINT_LIST, http_build_query($queryParam));
        $data = $this->fetch($url);

        if (!\is_array($data) || !isset($data['packageNames'])) {
            throw new PackagistException('Error getting data from packagist.org.');
        }
        return $data['packageNames'];
    }

    /**
     * Get a package
     *
     * @param string $vendorName
     * @param string $packageName
     * @return Package|false
     * @throws \Throwable
     */
    public function getPackage($vendorName, $packageName)
    {
        $url = sprintf(self::ENDPOINT_PACKAGE, $vendorName, $packageName);
        $data = $this->fetch($url);

        if (!\is_array($data) || !isset($data['package'])) {
            return false;
        }

        $package = Package::createFromAPIData($data['package']);
        return $package instanceof Package ? $package : false;
    }

    /**
     * Get README.md from repository
     *
     * @param string $repositoryUrl
     * @return null|string
     */
    public function getReadmeFromRepository($repositoryUrl)
    {
        // we don't need .git at the end of URL
        $repositoryUrl = preg_replace('~\.git$~i', '', $repositoryUrl);

        if (!preg_match('~^https?://(github\.com|gitlab\.com)/([^/]+/[^/]+)$~i', $repositoryUrl, $matches)) {
            return null;
        }

        $service = $matches[1];
        $package = $matches[2];

        $readmeNames = [
            'README.md',
            'readme.md',
            'Readme.md',
            'README',
            'readme',
            'README.MD',
            'ReadMe.md',
            'README.mkd',
        ];

        foreach ($readmeNames as $readmeName) {
            try {
                $result = $this->getRawFile($service, $package, $readmeName);
                if ($result !== false) {
                    return $result;
                }
            } catch (\Throwable $e) {
                Yii::error('Failed to read README from repository: ' . $repositoryUrl . ' ' . $e->getMessage());
            }
        }
        Yii::error('Failed to read README from repository: ' . $repositoryUrl);
        return null;
    }

    private function getRawFile($service, $package, $file)
    {
        switch ($service) {
            case 'github.com':
                $endpoint = self::ENDPOINT_GITHUB_RAW_FILE;
                break;
            case 'gitlab.com':
                $endpoint = self::ENDPOINT_GITLAB_RAW_FILE;
                break;
            default:
                throw new InvalidArgumentException("Getting files from $service is not supported.");
        }

        $url = sprintf($endpoint, $package, $file);
        return $this->fetch($url, true);
    }

    private function fetch($url, $raw = false)
    {
        $client = new Client([
            'transport' => CurlTransport::class
        ]);
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setOptions([
                CURLOPT_TIMEOUT => 10, // data receiving timeout
            ])
            ->send();

        if ($response->isOk) {
            return $raw ? $response->content : $response->data;
        }

        return false;
    }
}
