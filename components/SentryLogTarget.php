<?php

namespace app\components;

use Sentry\Severity;
use yii\log\Logger;
use yii\log\Target;

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
    public function export()
    {
        if (!\Sentry\SentrySdk::getCurrentHub()->getClient()) {
            return;
        }

        foreach ($this->messages as $message) {
            [$text, $level, $category, $timestamp] = $message;

            if ($text instanceof \Throwable) {
                $isApiDocError = $text instanceof \InvalidArgumentException && strpos($text->getMessage(), 'The tag') !== false;
                if ($isApiDocError) {
                    continue;
                }
                \Sentry\captureException($text);
            } else {
                $sentryLevel = $this->getSentryLevel($level);
                $messageText = is_string($text) ? $text : print_r($text, true);

                \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($messageText, $sentryLevel, $category, $timestamp) {
                    $scope->setExtra('category', $category);
                    $scope->setExtra('timestamp', $timestamp);
                    \Sentry\captureMessage($messageText, $sentryLevel);
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
