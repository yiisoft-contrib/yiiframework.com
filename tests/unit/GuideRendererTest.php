<?php

use app\apidoc\GuideRenderer;
use yii\apidoc\helpers\ApiMarkdown;
use yii\apidoc\helpers\ApiMarkdownLaTeX;
use yii\apidoc\models\Context;
use yii\helpers\FileHelper;

class GuideRendererTest extends \Codeception\Test\Unit
{
    public function testOfflineGuidePreservesCodeExamples(): void
    {
        $this->assertRenderedGuide(
            new \yii\apidoc\templates\bootstrap\GuideRenderer(['layout' => false]),
            'offline',
            'guide-next.html#details'
        );
    }

    public function testWebsiteGuidePreservesCodeExamples(): void
    {
        $renderer = new GuideRenderer(['guideUrl' => '/doc/guide/2.0/en']);
        $this->assertRenderedGuide($renderer, 'website', '/doc/guide/2.0/en/next#details');
    }

    private function assertRenderedGuide(
        \yii\apidoc\templates\html\GuideRenderer $renderer,
        string $name,
        string $expectedLink
    ): void
    {
        $directory = codecept_output_dir('guide-renderer-' . $name);
        FileHelper::createDirectory($directory);
        $source = $directory . '/example.md';
        $code = <<<'CODE'
<?php
echo $ready && $enabled;
<input name="example" <?= $attributes ?>>
&lt;literal entity&gt;
CODE;
        file_put_contents($source, "**Пример café 😀**\n\n[next](next.md#details)\n\n```php\n" . $code . "\n```\n");
        $renderer->apiContext = new Context();
        $previous = libxml_use_internal_errors(true);

        try {
            $renderer->render([$source], $directory);
            $document = new DOMDocument();
            self::assertTrue($document->loadHTML(
                '<?xml encoding="UTF-8">' . file_get_contents($directory . '/guide-example.html')
            ));
            self::assertSame([], libxml_get_errors());
            self::assertSame($code . "\n", $document->getElementsByTagName('code')->item(0)->textContent);
            self::assertSame('Пример café 😀', $document->getElementsByTagName('strong')->item(0)->textContent);
            self::assertSame($expectedLink, $document->getElementsByTagName('a')->item(0)->getAttribute('href'));
            self::assertSame(0, $document->getElementsByTagName('input')->length);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            ApiMarkdown::$renderer = null;
            ApiMarkdownLaTeX::$renderer = null;
        }
    }
}
