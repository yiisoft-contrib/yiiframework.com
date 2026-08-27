<?php

use app\apidoc\ApiMarkdown;
use app\apidoc\GuideRenderer;
use yii\apidoc\models\Context;

class ApiMarkdownTest extends \Codeception\Test\Unit
{
    public function testParsesMarkdownAfterPictureInsideParagraph(): void
    {
        $renderer = new GuideRenderer();
        $renderer->apiContext = new Context();
        ApiMarkdown::$renderer = $renderer;

        $markdown = <<<'MARKDOWN'
<p align="center">
    <picture>
        <img src="logo.svg" alt="Yii Framework">
    </picture>
</p>

This is an extension for [Yii](https://www.yiiframework.com).
MARKDOWN;

        $html = ApiMarkdown::process($markdown);

        self::assertStringContainsString(
            '<a href="https://www.yiiframework.com">Yii</a>',
            $html
        );
    }
}
