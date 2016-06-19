<?php

namespace app\controllers;

use app\components\Packagist;
use Yii;
use yii\data\Pagination;
use yii\data\Sort;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Расширения
 *
 * Class ExtensionController
 * @package app\controllers
 */
class ExtensionController extends Controller
{
    /**
     * Главная / поиск пакетов
     *
     * @param null|string $q
     * @param null|integer $page
     * @return string
     */
    public function actionIndex($q = null, $page = null)
    {
        $pagination = null;

        $queryString = trim($q);
        $sort = new Sort([
            'attributes' => [
                'downloads',
                'favers'
            ],
            'defaultOrder' => [
                'downloads' => SORT_DESC,
            ]
        ]);

        $keyCache = 'extension/index__dataPackagist_' . md5(serialize([$queryString, $page, $sort->getOrders()]));
        $dataPackagist = \Yii::$app->cache->get($keyCache);
        if ($dataPackagist === false) {
            $dataPackagist = Packagist::search($queryString, $page, $sort->getOrders());
            \Yii::$app->cache->set($keyCache, $dataPackagist, 300);
        }

        if ($dataPackagist['listPackage']) {
            $pagination = new Pagination([
                'totalCount' => $dataPackagist['totalPackage'],
                'defaultPageSize' => $dataPackagist['countOnPage'],
                'forcePageParam' => false
            ]);

            foreach ($dataPackagist['listPackage'] as &$package) {
                $package['urlPackage'] = Url::to([
                    'package',
                    'vendorName' => $package['vendorName'],
                    'packageName' => $package['packageName']
                ]);
            }
            unset($package);
        }

        if ($dataPackagist['messageError']) {
            \Yii::$app->session->setFlash('error', $dataPackagist['messageError']);
        }

        return $this->render('index', [
            'listPackage' => $dataPackagist['listPackage'],
            'totalPackage' => $dataPackagist['totalPackage'],
            'pagination' => $pagination,
            'sort' => $sort,
            'queryString' => $q
        ]);
    }

    /**
     * Открыть пакет
     *
     * @param string $vendorName
     * @param string $packageName
     * @param null|string $version
     *
     * @return string
     */
    public function actionPackage($vendorName, $packageName, $version = null)
    {
        $listVersion = [];
        $selectVersion = null;
        $selectVersionData = [];

        $keyCache = 'extension/package__package_' . md5(serialize([$vendorName, $packageName]));
        $package = \Yii::$app->cache->get($keyCache, 300);
        if ($package === false) {
            $package = Packagist::getPackage($vendorName, $packageName);
            \Yii::$app->cache->set($keyCache, $package, 300);
        }

        if ($package) {
            $listVersion = array_values($package['versions']);
            usort($listVersion, function($a, $b) {
                return $a['version_normalized'] < $b['version_normalized'];
            });

            foreach ($listVersion as $versionKey => $versionItem) {
                if (
                    (!is_null($version) && $version !== $versionItem['version']) ||
                    (is_null($version) && strpos($versionItem['version_normalized'], 'dev') !== false)
                ) {
                    continue;
                }

                $selectVersion = $versionItem;
                break;
            }

            if (is_null($selectVersion) && $listVersion) {
                $selectVersion = $listVersion[0];
            }

            if ($selectVersion) {
                foreach (['require', 'require-dev', 'suggest', 'provide', 'conflict', 'replace'] as $vName) {
                    $selectVersionData[$vName] = [];

                    if (!empty($selectVersion[$vName])) {
                        foreach ($selectVersion[$vName] as $kVersionItem =>  $vVersionItem) {
                            if (preg_match('/^([a-z\d\-_]+)\/([a-z\d\-_]+)$/i', $kVersionItem, $m)) {
                                $str = Html::a($kVersionItem, [
                                    'package',
                                    'vendorName' => $m[1],
                                    'packageName' => $m[2]
                                ]);
                            } else {
                                $str = Html::encode($kVersionItem);
                            }

                            $selectVersionData[$vName][] = ' - ' . $str . ' ' . Html::encode($vVersionItem);
                        }
                    }

                    if (!$selectVersionData[$vName]) {
                        $selectVersionData[$vName][] = '<small>[empty]</small>';
                    }
                }
            }

            $package['repoReadme'] = Packagist::getReadmeFromRepository($package['repository']);
        } else {
            \Yii::$app->session->setFlash('error', 'Error get data from packagist.org');
        }

        return $this->render('package', [
            'package' => $package,
            'listVersion' => $listVersion,
            'selectVersion' => $selectVersion,
            'selectVersionData' => $selectVersionData
        ]);
    }
}
