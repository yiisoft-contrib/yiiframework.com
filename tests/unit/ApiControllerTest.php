<?php

use app\commands\ApiController;
use yii\helpers\FileHelper;

class ApiControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider generationOutcomeProvider
     */
    public function testGenerationHoldsLockAndReleasesItOnExit(bool $fail): void
    {
        $directory = sys_get_temp_dir() . '/yii-api-generation-' . uniqid();
        FileHelper::createDirectory($directory);
        $originalAlias = Yii::getAlias('@app');
        $originalApplication = Yii::$app;
        $originalRuntime = Yii::getAlias('@runtime', false);

        try {
            $application = new \yii\console\Application([
                'id' => 'api-generation-test',
                'basePath' => $originalAlias,
                'runtimePath' => $directory,
                'params' => ['versions' => ['api' => ['2.0']], 'guide.baseUrl' => '/doc/guide'],
            ]);
            $controller = new class('api', $application) extends ApiController {
                public $onGenerate;

                public function actionIndex(array $sourceDirs, $targetDir)
                {
                    call_user_func($this->onGenerate);
                    file_put_contents($targetDir . '/types.json', '[]');
                }

                public function stdout($string)
                {
                    return strlen($string);
                }
            };
            Yii::setAlias('@app', $directory);
            $lockFile = $directory . '/data/api-2.0/.generate.lock';
            $calls = 0;
            $controller->onGenerate = function () use ($lockFile, $fail, &$calls) {
                ++$calls;
                self::assertFalse($this->canAcquireLock($lockFile));
                if ($fail) {
                    throw new RuntimeException('Generation failed.');
                }
            };

            try {
                $controller->actionGenerate('2.0');
                self::assertFalse($fail, 'Generation was expected to fail.');
            } catch (RuntimeException $exception) {
                if (!$fail) {
                    throw $exception;
                }
                self::assertSame('Generation failed.', $exception->getMessage());
            }

            self::assertSame($fail ? 1 : 2, $calls);
            self::assertFileExists($lockFile);
            self::assertTrue($this->canAcquireLock($lockFile));
        } finally {
            Yii::setAlias('@app', $originalAlias);
            Yii::setAlias('@runtime', $originalRuntime === false ? null : $originalRuntime);
            Yii::$app = $originalApplication;
            FileHelper::removeDirectory($directory);
        }
    }

    public static function generationOutcomeProvider(): array
    {
        return ['successful build' => [false], 'failed build' => [true]];
    }

    public function testGenerationPublishesMarkdownCacheOnce(): void
    {
        require_once __DIR__ . '/../_support/ApiGenerationCacheProbe.php';

        $directory = sys_get_temp_dir() . '/yii-api-cache-' . uniqid();
        $source = $directory . '/data/yii-2.0/framework';
        $target = $directory . '/data/api-2.0';
        FileHelper::createDirectory($source);
        FileHelper::createDirectory($target . '/cache');
        file_put_contents($source . '/Example.php', <<<'PHP'
<?php
namespace yii\docs;

class Example
{
    public function run(): void
    {
    }
}
PHP);
        $cacheFile = $target . '/cache/markdown.data';
        file_put_contents($cacheFile, 'previous cache');
        $previousCache = fopen($cacheFile, 'rb');
        $publishedCache = null;
        $originalApplication = Yii::$app;
        $originalAliases = Yii::$aliases;
        $originalRenderer = \yii\apidoc\helpers\ApiMarkdown::$renderer;
        $originalLaTeXRenderer = \yii\apidoc\helpers\ApiMarkdownLaTeX::$renderer;

        try {
            $application = new \yii\console\Application([
                'id' => 'api-cache-test',
                'basePath' => Yii::getAlias('@app'),
                'runtimePath' => $directory,
                'aliases' => [
                    '@app/data' => $directory . '/data',
                    '@bower' => '@vendor/bower-asset',
                ],
                'params' => [
                    'versions' => ['api' => ['2.0']],
                    'guide.baseUrl' => '/doc/guide',
                    'api.baseUrl' => '/doc/api',
                ],
            ]);
            $controller = new ApiGenerationCacheProbe('api', $application);
            $controller->interactive = false;
            $controller->afterRender = static function ($template, $location) use (&$publishedCache): void {
                if ($template === 'bootstrap') {
                    $publishedCache = fopen($location . '/cache/markdown.data', 'rb');
                }
            };

            self::assertSame(0, $controller->actionGenerate('2.0'));
            self::assertIsResource($publishedCache);
            clearstatcache(true, $cacheFile);
            self::assertNotSame(fstat($previousCache)['ino'], fstat($publishedCache)['ino']);
            self::assertSame(fstat($publishedCache)['ino'], stat($cacheFile)['ino']);
            self::assertSame('previous cache', stream_get_contents($previousCache));
            self::assertSame(0644, fileperms($cacheFile) & 0777);
            self::assertSame([], glob($target . '/cache/markdown-*'));
            $context = unserialize(file_get_contents($cacheFile));
            self::assertNotNull($context->getType('yii\\docs\\Example')->findSubject('run()'));
            self::assertFileExists($target . '/yii-docs-example.html');
            self::assertFileExists($target . '/json/typeMembers.json');
        } finally {
            if (is_resource($publishedCache)) {
                fclose($publishedCache);
            }
            fclose($previousCache);
            Yii::$app = $originalApplication;
            Yii::$aliases = $originalAliases;
            \yii\apidoc\helpers\ApiMarkdown::$renderer = $originalRenderer;
            \yii\apidoc\helpers\ApiMarkdownLaTeX::$renderer = $originalLaTeXRenderer;
            FileHelper::removeDirectory($directory);
        }
    }

    private function canAcquireLock(string $path): bool
    {
        $handle = fopen($path, 'c');
        try {
            return flock($handle, LOCK_EX | LOCK_NB);
        } finally {
            fclose($handle);
        }
    }
}
