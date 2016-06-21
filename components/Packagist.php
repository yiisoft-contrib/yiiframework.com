<?php

namespace app\components;

use yii\helpers\Json;

/**
 * Manger package yii2-extension from packagist.org
 *
 * @see https://packagist.org
 *
 * Class Packagist
 * @package app\components\extension
 */
class Packagist
{
    /**
     * Get list package
     *
     * @param string $q
     * @param null|integer $page
     * @param null|array $sort
     *
     * @return bool|array
     */
    public static function search($q, $page = null, $sort = null)
    {
        $listPackage = [];
        $totalPackage = 0;
        
        $listOrderBy = [
            [
                'sort' => 'downloads',
                'order' => 'desc'
            ]
        ];
        if ($sort !== null) {
            $listOrderBy = [];
            foreach ($sort as $kSort => $vSort) {
                $listOrderBy[] = [
                    'sort' => $kSort,
                    'order' => (SORT_ASC === $vSort)? 'asc': 'desc'
                ];
            }
        }

        $queryParam = [
            'type' => 'yii2-extension',
            'q' => $q,
            'orderBys' => $listOrderBy
        ];

        $queryParam['page'] = ($page === null)? 1: $page;

        try {
            $url = 'https://packagist.org/search.json?' . http_build_query($queryParam);
            $data = Json::decode(file_get_contents($url), true);
        } catch (\Exception $e) {
            return false;
        }

        if (!is_array($data) || array_diff(['results', 'total'], array_keys($data))) {
            return false;
        }

        $countOnPage = count($data['results']);
        foreach ($data['results'] as $vItem) {
            $package = [
                'name' => null,
                'description' => null,
                'url' => null,
                'repository' => null,
                'downloads' => 0,
                'favers' => 0,
            ];

            foreach (array_keys($package) as $key) {
                if (array_key_exists($key, $vItem)) {
                    $package[$key] = $vItem[$key];
                }
            }

            if (!preg_match('/^([a-z\d\-_]+)\/([a-z\d\-_]+)$/i', $package['name'], $m)) {
                continue;
            }

            $package['vendorName'] = $m[1];
            $package['packageName'] = $m[2];

            $listPackage[] = $package;
            $totalPackage = $data['total'];
        }

        return [
            'listPackage' => $listPackage,
            'totalPackage' => $totalPackage,
            'countOnPage' => $countOnPage
        ];
    }

    /**
     * Get package
     *
     * @param string $vendorName
     * @param string $packageName
     *
     * @return boolean|array
     */
    public static function getPackage($vendorName, $packageName)
    {
        try {
            $url = "https://packagist.org/packages/{$vendorName}/{$packageName}.json";
            $data = Json::decode(file_get_contents($url), true);
        } catch (\Exception $e) {
            return false;
        }

        if (!is_array($data) || !isset($data['package'])) {
            return false;
        }

        $dataPackage = $data['package'];

        $package = [
            'name' => null,
            'description' => null,
            'repository' => null,
            'versions' => [],
            'favers' => 0
        ];

        foreach (array_keys($package) as $key) {
            if (array_key_exists($key, $dataPackage)) {
                $package[$key] = $dataPackage[$key];
            }
        }

        if (!isset($package['versions']) || !is_array($package['versions'])) {
            $package['versions'] = [];
        }

        foreach (['total', 'monthly', 'daily'] as $key) {
            if (array_key_exists($key, $dataPackage['downloads'])) {
                $package['downloads'][$key] = $dataPackage['downloads'][$key];
            }
        }

        $package['time'] = (isset($dataPackage['time']))? strtotime($dataPackage['time']): null;

        return $package;
    }

    /**
     * Get readme for repository
     *
     * @param string $urlRepository
     * @return null|boolean|string
     */
    public static function getReadmeFromRepository($urlRepository)
    {
        $content = null;

        if (preg_match('/^https?:\/\/github\.com\/([^\/]+\/[^\/]+)\.git$/i', $urlRepository, $m)) {
            try {
                $content = file_get_contents("https://raw.githubusercontent.com/{$m[1]}/master/README.md");
            } catch (\Exception $e) {
                return false;
            }
        }

        return $content;
    }
}