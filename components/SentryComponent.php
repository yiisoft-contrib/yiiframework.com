<?php

namespace app\components;

use yii\base\BootstrapInterface;
use yii\base\Component;

/**
 * SentryComponent initializes the Sentry PHP SDK during application bootstrap.
 *
 * Configure in the application config:
 * ```php
 * 'components' => [
 *     'sentry' => [
 *         'class' => 'app\components\SentryComponent',
 *         'dsn' => 'https://...@sentry.io/...',
 *     ],
 * ],
 * 'bootstrap' => ['sentry'],
 * ```
 */
class SentryComponent extends Component implements BootstrapInterface
{
    /**
     * @var string|null Sentry DSN. If empty, Sentry will not be initialized.
     */
    public $dsn;

    /**
     * @var float Sample rate for performance tracing (0.0 to 1.0).
     */
    public $tracesSampleRate = 1.0;

    /**
     * @var float Sample rate for profiling, relative to traces_sample_rate (0.0 to 1.0).
     */
    public $profilesSampleRate = 1.0;

    /**
     * @var string|null Environment name. Defaults to YII_ENV if not set.
     */
    public $environment;

    /**
     * Bootstrap method called during application initialization.
     * Initializes the Sentry SDK if a DSN is configured.
     *
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        if (empty($this->dsn)) {
            return;
        }

        \Sentry\init([
            'dsn' => $this->dsn,
            'environment' => $this->environment ?: (defined('YII_ENV') ? YII_ENV : 'production'),
            'traces_sample_rate' => (float)$this->tracesSampleRate,
            'profiles_sample_rate' => (float)$this->profilesSampleRate,
        ]);

        // Register a global error handler to capture unhandled exceptions
        $previousHandler = set_exception_handler(null);
        restore_exception_handler();

        set_exception_handler(function (\Throwable $exception) use ($previousHandler) {
            if ($exception instanceof \yii\web\HttpException && $exception->statusCode < 500) {
                // Ignore 4xx HTTP exceptions to prevent Sentry spam
            } else {
                \Sentry\captureException($exception);
            }
            if ($previousHandler) {
                call_user_func($previousHandler, $exception);
            }
        });
    }
}
