<?php

namespace app\controllers;

use app\components\github\GithubRepoStatus;
use Yii;
use yii\data\ArrayDataProvider;

class StatusController extends BaseController
{
    public $sectionTitle = 'Release Statuses';

    private $githubClient;

    const REPOSITORIES = [
        // 2.0
        ['yiisoft', 'yii2'],
        ['yiisoft', 'yii2-app-basic'],
        ['yiisoft', 'yii2-app-advanced'],
        ['yiisoft', 'yii2-imagine'],
        ['yiisoft', 'yii2-jui'],
        ['yiisoft', 'yii2-faker'],
        ['yiisoft', 'yii2-smarty'],
        ['yiisoft', 'yii2-twig'],
        ['yiisoft', 'yii2-mongodb'],
        ['yiisoft', 'yii2-redis'],
        ['yiisoft', 'yii2-gii'],
        ['yiisoft', 'yii2-debug'],
        ['yiisoft', 'yii2-bootstrap'],
        ['yiisoft', 'yii2-swiftmailer'],
        ['yiisoft', 'yii2-httpclient'],
        ['yiisoft', 'yii2-authclient'],
        ['yiisoft', 'yii2-sphinx'],
        ['yiisoft', 'yii2-elasticsearch'],
        ['yiisoft', 'yii2-queue'],
        ['yiisoft', 'yii2-shell'],
        ['yiisoft', 'yii2-composer'],
        ['yiisoft', 'yii2-apidoc'],

        // 2.1
        ['yiisoft', 'yii2-docker'],
        ['yiisoft', 'yii2-app'],
        ['yiisoft', 'yii2-oracle'],
        ['yiisoft', 'yii2-mssql'],
        ['yiisoft', 'yii2-bootstrap4'],
        ['yiisoft', 'yii2-maskedinput'],
        ['yiisoft', 'yii2-collection'],
        ['yiisoft', 'yii2-phpunit'],
        ['yiisoft', 'yii2-jquery'],
        ['yiisoft', 'yii2-captcha'],


        // 1.1
        ['yiisoft', 'yii'],
    ];

    public function actionIndex()
    {
        $client = new \Github\Client();
        $tokenFile = Yii::getAlias('@app/data') . '/github.token';
        if (file_exists($tokenFile)) {
            $token = file_get_contents($tokenFile);
            $client->authenticate($token, null, \Github\Client::AUTH_URL_TOKEN);
        }

        $data = [];

        foreach (self::REPOSITORIES as $repository) {
            $githubRepoStatus = new GithubRepoStatus(Yii::$app->getCache(), $client, $repository[0], $repository[1]);
            $data[] = $githubRepoStatus->getInfo();
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'attributes' => [
                    'repository',
                    'no_release_for',
                    'latest',
                ],
                'defaultOrder' => ['repository' => SORT_ASC],
            ],
            'pagination' => false,
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}