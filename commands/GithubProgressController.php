<?php

namespace app\commands;

use app\components\github\GithubProgress;
use Github\Client as GithubClient;
use Github\Exception\RuntimeException;
use Yii;
use yii\console\Controller;

class GithubProgressController extends Controller
{
    const RETRY_ATTEMPTS_COUNT = 3;
    const RETRY_DELAY = 5; // seconds

    public function actionIndex()
    {
        foreach (GithubProgress::VERSIONS as $version) {
            for ($attempt = 1; $attempt <= static::RETRY_ATTEMPTS_COUNT; $attempt++) {
                $this->stdout("Getting data for version $version...\n");

                try {
                    $data = (new GithubProgress($version, new GithubClient()))->getData();
                    Yii::$app->cache->set("github_progress_data_$version", $data);

                    break;
                } catch (RuntimeException $e) {
                    $retryDelay = static::RETRY_DELAY;
                    $exception = (string) $e;
                    $this->stderr("Failed to get data for version $version:\n$exception\n\nRetrying in $retryDelay seconds...\n");
                    sleep(static::RETRY_DELAY);
                }
            }
        }
    }
}
