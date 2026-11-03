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
            self::assertTrue($this->canAcquireLock($lockFile));
            self::assertFileExists($lockFile);
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
