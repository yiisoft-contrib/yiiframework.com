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
    private const TIMEOUT_MESSAGE_FRAGMENTS = [
        'This may be the result of a timeout, or it could be a GitHub bug.',
        "We couldn't respond to your request in time.",
    ];

    public function actionIndex()
    {
        foreach (GithubProgress::VERSIONS as $version) {
            $this->fetchProgress($version);
        }
    }

    private function fetchProgress(string $version): void
    {
        for ($attempt = 1; $attempt <= self::ATTEMPTS_COUNT; $attempt++) {
            try {
                $data = (new GithubProgress($version, new GithubClient()))->getData();
                Yii::$app->cache->set("github_progress_data_$version", $data);

                return;
            } catch (RuntimeException $e) {
                if (!$this->isRetryableException($e) || $attempt === self::ATTEMPTS_COUNT) {
                    throw $e;
                }

                sleep(self::RETRY_DELAY);
            }
        }
    }

    private function isRetryableException(RuntimeException $exception): bool
    {
        // The GitHub client uses the HTTP status as the exception code.
        if (in_array($exception->getCode(), [500, 502, 503, 504], true)) {
            return true;
        }

        // Preserve timeout detection when the response has no HTTP status.
        if ($exception->getCode() !== 0) {
            return false;
        }

        foreach (self::TIMEOUT_MESSAGE_FRAGMENTS as $messageFragment) {
            if (strpos($exception->getMessage(), $messageFragment) !== false) {
                return true;
            }
        }

        return false;
    }
}
