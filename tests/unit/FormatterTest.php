<?php

use app\components\Formatter;
use yii\helpers\Html;
use yii\helpers\StringHelper;

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

    public function testSignsDecodedImageQueryString(): void
    {
        $formatter = new class extends Formatter {
            public function proxyImages($html)
            {
                return $this->replaceImageUrlForProxy($html);
            }
        };
        $sourceUrl = 'https://img.shields.io/github/actions/workflow/status/yiisoft/yii2-bootstrap5/static.yml?style=for-the-badge&label=Static';
        $sourceUri = substr($sourceUrl, strlen('https://img.shields.io/'));
        $originalParams = Yii::$app->params;
        Yii::$app->params['image-proxy'] = 'https://proxy.test';
        Yii::$app->params['image-proxy-secret'] = 'test-secret';

        try {
            $hash = rtrim(StringHelper::base64UrlEncode(md5($sourceUrl . ' test-secret', true)), '=');
            $proxyUrl = "https://proxy.test/img/$hash/https/img.shields.io/$sourceUri";

            self::assertSame(
                '<img src="' . Html::encode($proxyUrl) . '">',
                $formatter->proxyImages('<img src="' . Html::encode($sourceUrl) . '">')
            );
        } finally {
            Yii::$app->params = $originalParams;
        }
    }

}
