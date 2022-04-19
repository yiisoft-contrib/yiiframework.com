<?php

namespace app\commands;

use app\components\github\GithubProgress;
use Github\Client as GithubClient;
use Github\Exception\RuntimeException;
use Yii;
use yii\console\Controller;

final class GithubProgressController extends Controller
{
    private const ATTEMPTS_COUNT = 3;
    private const RETRY_DELAY = 90; // seconds

    public function actionIndex()
    {
        foreach (GithubProgress::VERSIONS as $version) {
            $this->fetchProgress($version);
        }
    }

    private function fetchProgress(string $version): void
    {
        $attempt = 0;
        $failures = 0;

        while ($attempt < self::ATTEMPTS_COUNT) {
            try {
                $data = (new GithubProgress($version, new GithubClient()))->getData();
                Yii::$app->cache->set("github_progress_data_$version", $data);

                break;
            } catch (RuntimeException $e) {
                $keyText = 'This may be the result of a timeout, or it could be a GitHub bug.';
                if (strpos($e->getMessage(), $keyText) === false) {
                    throw $e;
                }

                sleep(self::RETRY_DELAY);
                $failures++;
            }

            $attempt++;
        }

        if ($failures === self::ATTEMPTS_COUNT) {
            $this->stderr(sprintf(
                "Failed to get data for version %s because of timeout.\nRetried %s times with %s seconds interval.\n",
                $version,
                $attempt - 1,
                self::RETRY_DELAY
            ));
        }
    }
}
