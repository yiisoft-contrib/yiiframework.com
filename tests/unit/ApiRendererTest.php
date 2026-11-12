<?php

use app\apidoc\ApiRenderer;
use yii\apidoc\helpers\ApiMarkdown;
use yii\apidoc\helpers\ApiMarkdownLaTeX;
use yii\apidoc\models\Context;
use yii\helpers\FileHelper;

class ApiRendererTest extends \Codeception\Test\Unit
{
    public function testRendersMethodDocumentationWithWebsiteLinks(): void
    {
        $directory = codecept_output_dir('api-renderer');
        FileHelper::createDirectory($directory);
        $source = $directory . '/Example.php';
        file_put_contents($source, <<<'PHP'
<?php
namespace yii\docs;

/** An example API class. */
class Example
{
    /**
     * Greet a reader.
     * @param string $name the reader's name
     * @return string the greeting
     */
    public function greet(string $name): string
    {
        return 'Hello ' . $name;
    }
}
PHP);
        $context = new Context();
        $context->addFile($source);
        $context->processFiles();
        $context->updateReferences();
        $renderer = new ApiRenderer(['version' => '2.0', 'extensions' => []]);

        try {
            $renderer->render($context, $directory);
            $html = file_get_contents($directory . '/yii-docs-example.html');
            self::assertStringContainsString('Greet a reader.', $html);
            self::assertStringContainsString('$name', $html);
            self::assertStringContainsString('greet()', $html);
            self::assertStringContainsString($renderer->generateApiUrl('yii\\docs\\Example'), $html);
            self::assertFileExists($directory . '/titles.php');
            self::assertSame([], $context->errors);
        } finally {
            ApiMarkdown::$renderer = null;
            ApiMarkdownLaTeX::$renderer = null;
        }
    }
}
