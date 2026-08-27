<?php

use app\components\Formatter;

class FormatterTest extends \Codeception\Test\Unit
{
    public function testResolvesRelativeImageUrls(): void
    {
        $formatter = new class extends Formatter {
            public function resolveImages($html, $baseUrl)
            {
                return $this->resolveRelativeImageUrls($html, $baseUrl);
            }
        };

        $html = '<img src="docs/images/example.png"><img src="https://example.com/image.png">';

        self::assertSame(
            '<img src="https://raw.githubusercontent.com/yiisoft/package/master/docs/images/example.png">' .
            '<img src="https://example.com/image.png">',
            $formatter->resolveImages($html, 'https://raw.githubusercontent.com/yiisoft/package/master')
        );
    }

    public function testDoesNotProxyShieldsImages(): void
    {
        $formatter = new class extends Formatter {
            public function proxyUrl($sourceUrl)
            {
                return $this->generateProxyUrl($sourceUrl);
            }
        };
        $url = 'https://img.shields.io/github/actions/workflow/status/yiisoft/yii2-bootstrap5/build.yml?style=for-the-badge&amp;label=Build';

        self::assertSame($url, $formatter->proxyUrl($url));
    }
}
