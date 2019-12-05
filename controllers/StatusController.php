<?php

namespace app\controllers;

use app\components\github\GithubRepoStatus;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;

class StatusController extends BaseController
{
    public $sectionTitle = 'Release Statuses';

    const REPOSITORIES = [
        '2.0' => [
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
            ['yiisoft', 'yii2-bootstrap4'],
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
        ],
        '3.0' => [
            // core packages
            ['yiisoft', 'di'],
            ['yiisoft', 'injector'],
            ['yiisoft', 'factory'],
            ['yiisoft', 'access'],
            ['yiisoft', 'event-dispatcher'],
            ['yiisoft', 'security'],
            ['yiisoft', 'data'],
            ['yiisoft', 'profiler'],
            ['yiisoft', 'aliases'],
            ['yiisoft', 'serializer'],
            ['yiisoft', 'network-utilities'],
            ['yiisoft', 'auth'],

            // cache
            ['yiisoft', 'cache'],
            ['yiisoft', 'cache-apcu'],
            ['yiisoft', 'cache-wincache'],
            ['yiisoft', 'cache-file'],
            ['yiisoft', 'cache-db'],

            // RBAC
            ['yiisoft', 'rbac'],
            ['yiisoft', 'rbac-db'],
            ['yiisoft', 'rbac-php'],

            // log
            ['yiisoft', 'log'],
            ['yiisoft', 'log-target-db'],
            ['yiisoft', 'log-target-email'],
            ['yiisoft', 'log-target-file'],
            ['yiisoft', 'log-target-syslog'],

            // i18n
            ['yiisoft', 'i18n'],
            ['yiisoft', 'i18n-message-php'],
            ['yiisoft', 'i18n-message-gettext'],
            ['yiisoft', 'i18n-formatter-intl'],

            // queue
            ['yiisoft', 'yii-queue'],
            ['yiisoft', 'yii-queue-interop'],

            // mutex
            ['yiisoft', 'mutex'],
            ['yiisoft', 'mutex-file'],
            ['yiisoft', 'mutex-db-pgsql'],
            ['yiisoft', 'mutex-db-oracle'],
            ['yiisoft', 'mutex-db-mysql'],

            // mailer
            ['yiisoft', 'mailer'],
            ['yiisoft', 'mailer-swiftmailer'],

            // helpers
            ['yiisoft', 'arrays'],
            ['yiisoft', 'strings'],
            ['yiisoft', 'files'],
            ['yiisoft', 'var-dumper'],
            ['yiisoft', 'html'],
            ['yiisoft', 'json'],

            // console
            ['yiisoft', 'yii-console'],

            // api
            ['yiisoft', 'yii-rest'],

            // db
            ['yiisoft', 'db'],
            ['yiisoft', 'db-mysql'],
            ['yiisoft', 'db-pgsql'],
            ['yiisoft', 'db-sqlite'],
            ['yiisoft', 'db-mssql'],
            ['yiisoft', 'db-oracle'],
            ['yiisoft', 'db-mongodb'],
            ['yiisoft', 'active-record'],
            //['yiisoft', 'migration'],
            ['yiisoft', 'yii-cycle'],

            // router
            ['yiisoft', 'router'],
            ['yiisoft', 'router-fastroute'],

            // web
            ['yiisoft', 'yii-web'],
            ['yiisoft', 'view'],
            ['yiisoft', 'yii-jquery'],
            ['yiisoft', 'yii-masked-input'],
            ['yiisoft', 'yii-dataview'],
            ['yiisoft', 'yii-debug'],
            ['yiisoft', 'yii-gii'],
            ['yiisoft', 'yii-bootstrap4'],

            ['yiisoft', 'yii-captcha'],

            ['yiisoft', 'yii-auth-client'],

            // project templates
            ['yiisoft', 'yii-base-api'],
            ['yiisoft', 'yii-base-web'],
            ['yiisoft', 'yii-project-template'],

            // demo
            ['yiisoft', 'yii-demo'],

            // other
            ['yiisoft', 'yii-docker'],
            ['yiisoft', 'validator'],
            ['yiisoft', 'friendly-exception'],

            // repository template
            ['yiisoft', 'template'],

            // requirements checker
            ['yiisoft', 'requirements'],
        ],

        '1.1' => [
            ['yiisoft', 'yii'],
        ]
    ];

    /**
     * @param string $version
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($version = '2.0')
    {
        $versions = array_keys(self::REPOSITORIES);

        if (!in_array($version, $versions, true)) {
            throw new NotFoundHttpException('The requested version does not exist.');
        }

        $client = new \Github\Client();
        $tokenFile = Yii::getAlias('@app/data') . '/github.token';
        if (file_exists($tokenFile)) {
            $token = trim(file_get_contents($tokenFile));
            $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
        }

        $githubRepoStatus = new GithubRepoStatus(Yii::$app->getCache(), $client, self::REPOSITORIES[$version], $version);

        $data = $githubRepoStatus->getData();

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

        return $this->render('index', [
            'version' => $version,
            'dataProvider' => $dataProvider,
            'versions' => $versions,
        ]);
    }
}
