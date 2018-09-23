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
        ['yiisoft', 'yii2-docker'],

        // 3.0 framework
        ['yiisoft', 'yii-core'],
        ['yiisoft', 'yii-console'],
        ['yiisoft', 'yii-web'],
        ['yiisoft', 'yii-rest'],

        // 3.0 templates
        ['yiisoft', 'yii-project-template'],
        ['yiisoft', 'yii-base-web'],
        ['yiisoft', 'yii-base-api'],
        //['yiisoft', 'yii-base-cli'],

        // 3.0 widgets and wrappers
        ['yiisoft', 'yii-bootstrap3'],
        ['yiisoft', 'yii-bootstrap4'],
        ['yiisoft', 'yii-masked-input'],

        // 3.0 tools
        ['yiisoft', 'yii-debug'],
        ['yiisoft', 'yii-gii'],

        // 3.0 others
        ['yiisoft', 'yii-jquery'],
        ['yiisoft', 'yii-captcha'],
        ['yiisoft', 'yii-swiftmailer'],
        ['yiisoft', 'yii-twig'],
        ['yiisoft', 'yii-http-client'],
        ['yiisoft', 'yii-auth-client'],

        // libraries
        ['yiisoft', 'log'],
        ['yiisoft', 'di'],
        ['yiisoft', 'cache'],
        ['yiisoft', 'db'],
        ['yiisoft', 'active-record'],
        ['yiisoft', 'rbac'],

        // 3.0 DB drivers
        ['yiisoft', 'db-mysql'],
        ['yiisoft', 'db-mssql'],
        ['yiisoft', 'db-pgsql'],
        ['yiisoft', 'db-sqlite'],
        ['yiisoft', 'db-oracle'],

        // 3.0 NoSQL DB drivers
        ['yiisoft', 'db-sphinx'],
        ['yiisoft', 'db-redis'],
        ['yiisoft', 'db-mongodb'],
        ['yiisoft', 'db-elasticsearch'],

        // 1.1
        ['yiisoft', 'yii'],
    ];

    public function actionIndex()
    {
        $client = new \Github\Client();
        $tokenFile = Yii::getAlias('@app/data') . '/github.token';
        if (file_exists($tokenFile)) {
            $token = file_get_contents($tokenFile);
            $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
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