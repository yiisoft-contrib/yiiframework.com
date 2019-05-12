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
            // 3.0 framework
            ['yiisoft', 'yii-core'],
            ['yiisoft', 'yii-console'],
            ['yiisoft', 'yii-web'],
            ['yiisoft', 'yii-rest'],

            // 3.0 templates
            ['yiisoft', 'yii-project-template'],
            ['yiisoft', 'yii-base-web'],
            ['yiisoft', 'yii-base-api'],
            ['yiisoft', 'yii-base-cli'],

            // 3.0 widgets and wrappers
            ['yiisoft', 'yii-bootstrap3'],
            ['yiisoft', 'yii-bootstrap4'],
            ['yiisoft', 'yii-dataview'],
            ['yiisoft', 'yii-masked-input'],

            // 3.0 tools
            ['yiisoft', 'yii-debug'],
            ['yiisoft', 'yii-gii'],

            // 3.0 others
            ['yiisoft', 'yii-auth-client'],
            ['yiisoft', 'yii-captcha'],
            ['yiisoft', 'yii-http-client'],
            ['yiisoft', 'yii-jquery'],
            ['yiisoft', 'yii-queue'],
            ['yiisoft', 'yii-swiftmailer'],
            ['yiisoft', 'yii-twig'],

            // libraries
            ['yiisoft', 'di'],
            ['yiisoft', 'cache'],
            ['yiisoft', 'active-record'],
            ['yiisoft', 'rbac'],
            ['yiisoft', 'view'],

            // log
            ['yiisoft', 'log'],
            ['yiisoft', 'log-target-db'],
            ['yiisoft', 'log-target-email'],
            ['yiisoft', 'log-target-file'],
            ['yiisoft', 'log-target-syslog'],

            // mutex
            ['yiisoft', 'mutex'],
            ['yiisoft', 'mutex-db-mysql'],
            ['yiisoft', 'mutex-db-oracle'],
            ['yiisoft', 'mutex-db-pgsql'],
            ['yiisoft', 'mutex-db-redis'],
            ['yiisoft', 'mutex-file'],

            // i18n
            ['yiisoft', 'i18n'],
            ['yiisoft', 'i18n-formatter-intl'],
            ['yiisoft', 'i18n-message-gettext'],
            ['yiisoft', 'i18n-message-php'],


            // 3.0 DB drivers
            ['yiisoft', 'db'],

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

            // Helpers
            ['yiisoft', 'arrays'],
            ['yiisoft', 'strings'],
            ['yiisoft', 'var-dumper'],
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