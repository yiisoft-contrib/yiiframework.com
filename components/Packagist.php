<?php

namespace app\components;

use yii\base\Exception;
use yii\helpers\Json;

/**
 * Менеджер пакетов yii2-extension из packagist.org
 *
 * @see https://packagist.org
 *
 * Class Packagist
 * @package app\components\extension
 */
class Packagist
{
    public $vendorName;
    public $packageName;
    public $description;

    /**
     * Поулчить список пакетов
     *
     * @param string $q
     * @param null|integer $page
     * @param null|array $sort
     *
     * @return array
     */
    public static function search($q, $page = null, $sort = null)
    {
        $orderBys = [
            0 => [
                'sort' => 'downloads',
                'order' => 'desc',
            ]
        ];
        if (!is_null($sort)) {
            $orderBys = [];
            foreach ($sort as $kSort => $vSort) {
                $orderBys[] = [
                    'sort' => $kSort,
                    'order' => SORT_ASC == $vSort? 'asc': 'desc'
                ];
            }
        }
        $queryParam = [
            'type' => 'yii2-extension',
            'page' => $page,
            'q' => $q,
            'orderBys' => $orderBys
        ];

        if (!is_null($page)) {
            $queryParam['page'] = $page;
        }

        $url = 'https://packagist.org/search.json?' . http_build_query($queryParam);
        $data = Json::decode(@file_get_contents($url), true);

        $listPackage = [];
        $totalPackage = 0;
        $countOnPage = 0;
        $messageError = null;
        try {
            if (!is_array($data) || array_diff(['results', 'total'], array_keys($data))) {
                throw new Exception('');
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
        } catch (Exception $e) {
            $messageError = ($e->getMessage())?: 'Error get data from packagist.org';
        }

        return [
            'listPackage' => $listPackage,
            'totalPackage' => $totalPackage,
            'countOnPage' => $countOnPage,
            'messageError' => $messageError
        ];
    }

    /**
     * Получить пакет
     *
     * @param $vendorName
     * @param $packageName
     *
     * @return boolean|array
     */
    public static function getPackage($vendorName, $packageName)
    {
        $url = "https://packagist.org/packages/{$vendorName}/{$packageName}.json";
        $data = Json::decode(@file_get_contents($url), true);

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
     * Получить readme для репозитория
     *
     * @param string $urlRepository
     * @return null|string
     */
    public static function getReadmeFromRepository($urlRepository)
    {
        $content = null;

        if (preg_match('/^https?:\/\/github\.com\/([^\/]+\/[^\/]+)\.git$/i', $urlRepository, $m)) {
            $content = @file_get_contents("https://raw.githubusercontent.com/{$m[1]}/master/README.md");
        }

        return $content;
    }
}