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
 * Extension page
 *
 * Class ExtensionController
 * @package app\controllers
 */
class ExtensionController extends Controller
{
    /**
     * Search package
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
            \Yii::$app->cache->set($keyCache, $dataPackagist, \Yii::$app->params['cacheDuration.extensionController.searchPackage']);
        }

        $listPackage = [];
        $totalPackage = 0;

        if ($dataPackagist) {
            $listPackage = $dataPackagist['listPackage'];
            $totalPackage = $dataPackagist['totalPackage'];

            if ($listPackage) {
                $pagination = new Pagination([
                    'totalCount' => $totalPackage,
                    'defaultPageSize' => $dataPackagist['countOnPage'],
                    'forcePageParam' => false
                ]);

                foreach ($listPackage as &$package) {
                    $package['urlPackage'] = Url::to([
                        'package',
                        'vendorName' => $package['vendorName'],
                        'packageName' => $package['packageName']
                    ]);

                    $package['repositoryHost'] = parse_url($package['repository'], PHP_URL_HOST);
                }
                unset($package);
            }
        } else {
            \Yii::$app->session->setFlash('error', 'Bad response from the server packagist.org');
        }

        return $this->render('index', [
            'listPackage' => $listPackage,
            'totalPackage' => $totalPackage,
            'pagination' => $pagination,
            'sort' => $sort,
            'queryString' => $q
        ]);
    }

    /**
     * Open package
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

        $keyCache = 'extension/package__package_' . md5(serialize([$vendorName, $packageName]));
        $package = \Yii::$app->cache->get($keyCache);
        if ($package === false) {
            $package = Packagist::getPackage($vendorName, $packageName);
            if ($package) {
                $package['repoReadme'] = Packagist::getReadmeFromRepository($package['repository']);
            }

            \Yii::$app->cache->set($keyCache, $package, \Yii::$app->params['cacheDuration.extensionController.openPackage']);
        }

        if ($package) {
            $package['repositoryHost'] = parse_url($package['repository'], PHP_URL_HOST);

            $listVersion = array_values($package['versions']);
            usort($listVersion, function($a, $b) {
                return ($a['version_normalized'] < $b['version_normalized'])? 1: -1;
            });

            foreach ($listVersion as $versionKey => $versionItem) {
                if (
                    ($version !== null && $version !== $versionItem['version']) ||
                    ($version === null && mb_strpos($versionItem['version_normalized'], 'dev') !== false)
                ) {
                    continue;
                }

                $selectVersion = $versionItem;
                break;
            }

            if ($selectVersion === null && $listVersion) {
                $selectVersion = $listVersion[0];
            }
        } else {
            \Yii::$app->session->setFlash('error', 'Bad response from the server packagist.org');
        }

        return $this->render('package', [
            'package' => $package,
            'listVersion' => $listVersion,
            'selectVersion' => $selectVersion
        ]);
    }
}
