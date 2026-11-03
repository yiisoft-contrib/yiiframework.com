<?php

use app\apidoc\ApiRenderer;
use yii\apidoc\models\ClassDoc;
use yii\apidoc\models\Context;
use yii\helpers\FileHelper;

class ApiRendererTest extends \PHPUnit\Framework\TestCase
{
    private $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/yii-api-renderer-' . uniqid();
        FileHelper::createDirectory($this->directory . '/cache');
    }

    protected function tearDown(): void
    {
        FileHelper::removeDirectory($this->directory);
    }

    public function testPublishesReadableCacheAfterRenderingCompletes(): void
    {
        $cacheFile = $this->directory . '/cache/markdown.data';
        file_put_contents($cacheFile, 'previous cache');
        $renderer = $this->createRenderer();
        $renderer->beforeRender = function () use ($cacheFile) {
            self::assertSame('previous cache', file_get_contents($cacheFile));
        };
        $context = new Context();
        $context->classes['yii\\example\\Example'] = new ClassDoc(null, null, ['name' => 'yii\\example\\Example']);

        $renderer->render($context, $this->directory);

        self::assertFileExists($this->directory . '/titles.php');
        self::assertNotNull(unserialize(file_get_contents($cacheFile))->getType('yii\\example\\Example'));
        self::assertSame(0644, fileperms($cacheFile) & 0777);
        self::assertSame([], glob($this->directory . '/cache/markdown-*'));
    }

    public function testRenderingFailureKeepsPreviousCache(): void
    {
        $cacheFile = $this->directory . '/cache/markdown.data';
        file_put_contents($cacheFile, 'previous cache');
        $renderer = $this->createRenderer();
        $renderer->beforeRender = function () {
            throw new RuntimeException('Rendering failed.');
        };
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rendering failed.');

        try {
            $renderer->render(new Context(), $this->directory);
        } finally {
            self::assertSame('previous cache', file_get_contents($cacheFile));
            self::assertSame([], glob($this->directory . '/cache/markdown-*'));
        }
    }

    public function testPublicationFailureRemovesTemporaryCache(): void
    {
        // A directory at the destination makes rename fail without relying on filesystem permissions.
        $cacheFile = $this->directory . '/cache/markdown.data';
        mkdir($cacheFile);
        $renderer = $this->createRenderer();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot publish Markdown API cache');

        try {
            $renderer->render(new Context(), $this->directory);
        } finally {
            self::assertDirectoryExists($cacheFile);
            self::assertSame([], glob($this->directory . '/cache/markdown-*'));
        }
    }

    private function createRenderer(): ApiRenderer
    {
        return new class(['version' => '2.0', 'extensions' => []]) extends ApiRenderer {
            public $beforeRender;

            protected function renderWithLayout($viewFile, $params)
            {
                if ($this->beforeRender !== null) {
                    call_user_func($this->beforeRender);
                }

                return 'API page';
            }
        };
    }
}
