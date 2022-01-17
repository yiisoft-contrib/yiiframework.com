<?php

namespace app\controllers;

use app\components\github\GithubProgress;
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
    public function actionIndex(string $version = '2.0'): string
    {
        if (!in_array($version, GithubProgress::VERSIONS, true)) {
            throw new NotFoundHttpException('The requested version does not exist.');
        }

        $data = Yii::$app->cache->get("github_progress_data_$version");
        if (!$data) {
            throw new NotFoundHttpException('Data not found.');
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'attributes' => ['repository', 'no_release_for', 'latest'],
                'defaultOrder' => ['repository' => SORT_ASC],
            ],
            'pagination' => false,
        ]);

        return $this->render('index', [
            'version' => $version,
            'dataProvider' => $dataProvider,
            'versions' => GithubProgress::VERSIONS,
        ]);
    }

    public function actionYii3Progress()
    {
        $this->layout = 'fullpage';
        $this->sectionTitle = 'How about progress on Yii3 development?';

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

            $allCount++;
        }

        return $this->render('yii3-progress', [
            'progress' => "{$releasedCount}/{$allCount}",
            'progressPercent' => $allCount > 0  ? round(100 * $releasedCount / $allCount) : 0,
        ]);
    }
}
