<?php

namespace app\controllers;

use app\components\github\GithubProgress;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;

class GithubProgressController extends BaseController
{
    public $sectionTitle = 'Release Statuses';

    /**
     * @param string $version
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(string $version = '2.0'): string
    {
        if (!in_array($version, GithubProgress::VERSIONS, true)) {
            throw new NotFoundHttpException('The requested version does not exist.');
        }

        $data = Yii::$app->cache->get("github_progress_data_$version");
        if (!$data) {
            throw new NotFoundHttpException('Data not found.');
        }

        $dataProvider = $version === '3.0'
            ? $this->getDevelopedFrameworkDataProvider($data)
            : $this->getReleasedFrameworkDataProvider($data);

        return $this->render('index', [
            'version' => $version,
            'dataProvider' => $dataProvider,
            'versions' => GithubProgress::VERSIONS,
        ]);
    }

    public function actionYii3Progress()
    {
        $this->layout = 'fullpage';
        $this->sectionTitle = 'Yii3 release status';

        $data = Yii::$app->cache->get('github_progress_data_3.0');
        if (!$data) {
            throw new NotFoundHttpException('Data not found.');
        }

        $allCount = 0;
        $releasedCount = 0;
        foreach ($data as $item) {
            if (!empty($item['latest'])) {
                $releasedCount++;
            }

            if (!$item['optionalForFrameworkAnnounce']) {
                $allCount++;
            }
        }

        return $this->render('yii3-progress', [
            'progress' => "{$releasedCount}/{$allCount}",
            'progressPercent' => $allCount > 0  ? round(100 * $releasedCount / $allCount) : 0,
        ]);
    }

    private function getReleasedFrameworkDataProvider($data)
    {
        return new ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'attributes' => ['repository', 'no_release_for', 'latest'],
                'defaultOrder' => ['repository' => SORT_ASC],
            ],
            'pagination' => false,
        ]);
    }

    private function getDevelopedFrameworkDataProvider($data)
    {
        return new ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'attributes' => [
                    'repository',
                    'no_release_for' => [
                        'asc' => [
                            'no_release_for' => SORT_ASC,
                            'optionalForFrameworkAnnounce' => SORT_ASC,
                            'repository' => SORT_ASC,
                        ],
                        'desc' => [
                            'no_release_for' => SORT_DESC,
                            'optionalForFrameworkAnnounce' => SORT_DESC,
                            'repository' => SORT_ASC,
                        ],
                        'default' => SORT_DESC,
                        'label' => 'No Release For',
                    ],
                    'latest',
                    'optionalForFrameworkAnnounce',
                ],
                'defaultOrder' => ['no_release_for' => SORT_ASC],
            ],
            'pagination' => false,
        ]);
    }
}
