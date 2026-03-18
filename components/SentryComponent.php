<?php

namespace app\components;

use Throwable;
use yii\base\BootstrapInterface;
use yii\base\Component;

use function Sentry\captureException;
use function Sentry\init;

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
    public ?string $dsn;

    /**
     * @var float Sample rate for performance tracing (0.0 to 1.0).
     */
    public float $tracesSampleRate = 1.0;

    /**
     * @var float Sample rate for profiling, relative to traces_sample_rate (0.0 to 1.0).
     */
    public float $profilesSampleRate = 1.0;

    /**
     * @var string|null Environment name. Defaults to YII_ENV if not set.
     */
    public ?string $environment;

    /**
     * Bootstrap method called during application initialization.
     * Initializes the Sentry SDK if a DSN is configured.
     *
     * @param \yii\base\Application $app
     */
    public function bootstrap($app): void
    {
        if (empty($this->dsn)) {
            return;
        }

        init([
            'dsn' => $this->dsn,
            'environment' => $this->environment ?: (defined('YII_ENV') ? YII_ENV : 'production'),
            'traces_sample_rate' => (float)$this->tracesSampleRate,
            'profiles_sample_rate' => (float)$this->profilesSampleRate,
        ]);

        // Register a global error handler to capture unhandled exceptions
        $previousHandler = set_exception_handler(null);
        restore_exception_handler();

        set_exception_handler(static function (Throwable $exception) use ($previousHandler) {
            captureException($exception);
            if ($previousHandler) {
                $previousHandler($exception);
            }
        });
    }
}
