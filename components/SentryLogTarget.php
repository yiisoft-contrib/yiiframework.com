<?php

namespace app\components;

use Sentry\SentrySdk;
use Sentry\Severity;
use Sentry\State\Scope;
use Throwable;
use yii\log\Logger;
use yii\log\Target;

use function Sentry\captureException;
use function Sentry\captureMessage;
use function Sentry\withScope;

/**
 * SentryLogTarget sends Yii2 log messages to Sentry.
 *
 * Usage in config:
 * ```php
 * 'log' => [
 *     'targets' => [
 *         [
 *             'class' => 'app\components\SentryLogTarget',
 *             'levels' => ['error', 'warning'],
 *         ],
 *     ],
 * ],
 * ```
 */
class SentryLogTarget extends Target
{
    /**
     * Exports log messages to Sentry.
     */
    public function export(): void
    {
        if (!SentrySdk::getCurrentHub()->getClient()) {
            return;
        }

        foreach ($this->messages as $message) {
            [$text, $level, $category, $timestamp] = $message;

            if ($text instanceof Throwable) {
                captureException($text);
            } else {
                $sentryLevel = $this->getSentryLevel($level);
                $messageText = is_string($text) ? $text : print_r($text, true);

                withScope(function (Scope $scope) use ($messageText, $sentryLevel, $category, $timestamp) {
                    $scope->setExtra('category', $category);
                    $scope->setExtra('timestamp', $timestamp);
                    captureMessage($messageText, $sentryLevel);
                });
            }
        }
    }

    /**
     * Map Yii2 log level to Sentry severity.
     *
     * @param int $level Yii2 log level constant
     * @return Severity
     */
    protected function getSentryLevel(int $level): Severity
    {
        switch ($level) {
            case Logger::LEVEL_ERROR:
                return Severity::error();
            case Logger::LEVEL_WARNING:
                return Severity::warning();
            case Logger::LEVEL_INFO:
                return Severity::info();
            default:
                return Severity::debug();
        }
    }
}
