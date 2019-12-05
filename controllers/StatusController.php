<?php

namespace app\controllers;

use app\components\github\GithubRepoStatus;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;

class StatusController extends BaseController
{
    public $sectionTitle = 'Release Statuses';

    /**
     * @param string $version
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($version = '2.0')
    {
        $packages = Yii::$app->params['packages'];

        $versions = array_keys($packages);

        if (!in_array($version, $versions, true)) {
            throw new NotFoundHttpException('The requested version does not exist.');
        }

        $client = new \Github\Client();
        $tokenFile = Yii::getAlias('@app/data') . '/github.token';
        if (file_exists($tokenFile)) {
            $token = trim(file_get_contents($tokenFile));
            $client->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
        }

        $githubRepoStatus = new GithubRepoStatus(Yii::$app->getCache(), $client, $packages[$version], $version);

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
