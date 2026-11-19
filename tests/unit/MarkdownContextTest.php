<?php

use app\apidoc\ApiMarkdown;
use app\apidoc\GuideRenderer;
use app\apidoc\MarkdownContext;
use app\components\Formatter;
use yii\apidoc\models\ClassDoc;
use yii\apidoc\models\ConstDoc;
use yii\apidoc\models\Context;
use yii\apidoc\models\EventDoc;
use yii\apidoc\models\InterfaceDoc;
use yii\apidoc\models\MethodDoc;
use yii\apidoc\models\PropertyDoc;
use yii\apidoc\models\TraitDoc;
use yii\helpers\FileHelper;

class UnexpectedApidocCacheRead extends Context
{
    public function __wakeup(): void
    {
        throw new RuntimeException('The full API cache must not be unserialized.');
    }
}

class MarkdownContextTest extends \PHPUnit\Framework\TestCase
{
    public function testFormatterUsesCompactCacheInsteadOfFullApiContext(): void
    {
        $context = new Context();
        $context->classes['Example'] = new ClassDoc(null, null, ['name' => 'Example']);

        self::assertStringContainsString(
            'href="/doc/api/2.0/example"',
            $this->renderWithCache(serialize(MarkdownContext::create($context)))
        );
    }

    /**
     * @dataProvider invalidCacheProvider
     */
    public function testFormatterFallsBackForMissingOrInvalidCache(?string $cacheData): void
    {
        $html = $this->renderWithCache($cacheData);

        self::assertStringContainsString('<strong>Text</strong>', $html);
        self::assertStringContainsString('<span class="broken-link">Example</span>', $html);
    }

    public static function invalidCacheProvider(): array
    {
        return [
            'missing' => [null],
            'truncated' => ['O:25:"yii\\apidoc\\models\\Context":9:{'],
            'false' => [serialize(false)],
            'wrong type' => [serialize(new stdClass())],
            'missing class' => ['O:19:"MissingCacheContext":0:{}'],
        ];
    }

    private function renderWithCache(?string $cacheData): string
    {
        $directory = sys_get_temp_dir() . '/yii-markdown-context-' . uniqid();
        mkdir($directory . '/data/api-2.0/cache', 0777, true);
        if ($cacheData !== null) {
            file_put_contents($directory . '/data/api-2.0/cache/markdown.data', $cacheData);
        }
        file_put_contents($directory . '/data/api-2.0/cache/apidoc.data', serialize(new UnexpectedApidocCacheRead()));
        $originalAlias = Yii::getAlias('@app');
        $originalRenderer = ApiMarkdown::$renderer;
        $originalApplication = Yii::$app;
        $originalRuntime = Yii::getAlias('@runtime', false);

        try {
            ApiMarkdown::$renderer = new GuideRenderer(['apiUrl' => '/doc/api/2.0']);
            $formatter = new Formatter(['locale' => 'en-US', 'booleanFormat' => ['No', 'Yes']]);
            new \yii\console\Application([
                'id' => 'markdown-context-test',
                'basePath' => $originalAlias,
                'runtimePath' => $directory,
            ]);
            Yii::setAlias('@app', $directory);

            return $formatter->asGuideMarkdown('[[Example]] **Text**');
        } finally {
            Yii::setAlias('@app', $originalAlias);
            Yii::setAlias('@runtime', $originalRuntime === false ? null : $originalRuntime);
            Yii::$app = $originalApplication;
            ApiMarkdown::$renderer = $originalRenderer;
            FileHelper::removeDirectory($directory);
        }
    }

    public function testPreservesApiLinksWithoutLoadingDocumentation(): void
    {
        $context = new Context();
        $type = new ClassDoc(null, null, ['name' => 'yii\\example\\ParentType', 'namespace' => 'yii\\example']);
        $type->description = str_repeat('Documentation that Markdown links do not need. ', 10000);
        $type->methods['run'] = new MethodDoc(null, null, [
            'name' => 'run',
            'definedBy' => $type->name,
            'sourceCode' => str_repeat('// Method source code. ', 10000),
        ]);
        $type->properties['label'] = new PropertyDoc(null, null, ['name' => 'label', 'definedBy' => $type->name]);
        $type->constants['VALUE'] = new ConstDoc(null, null, ['name' => 'VALUE', 'definedBy' => $type->name]);
        $type->events['EVENT_RUN'] = new EventDoc(null, null, ['name' => 'EVENT_RUN', 'definedBy' => $type->name]);
        $context->classes[$type->name] = $type;

        $child = new ClassDoc(null, null, ['name' => 'yii\\example\\ChildType', 'parentClass' => $type->name]);
        $child->methods = $type->methods;
        $context->classes[$child->name] = $child;
        $interface = new InterfaceDoc(null, null, ['name' => 'yii\\example\\Contract']);
        $context->interfaces[$interface->name] = $interface;
        $trait = new TraitDoc(null, null, ['name' => 'yii\\example\\ExampleTrait']);
        $context->traits[$trait->name] = $trait;

        $markdown = '[[yii\\example\\ParentType]] [[yii\\example\\ChildType::run()]] '
            . '[[yii\\example\\ParentType::$label]] [[yii\\example\\ParentType::VALUE]] '
            . '[[yii\\example\\ParentType::EVENT_RUN]] [[yii\\example\\Contract]] [[yii\\example\\ExampleTrait]]';
        $originalRenderer = ApiMarkdown::$renderer;

        try {
            $renderer = new GuideRenderer(['apiUrl' => '/doc/api/2.0']);
            ApiMarkdown::$renderer = $renderer;
            $renderer->apiContext = $context;
            $expected = ApiMarkdown::process($markdown);
            self::assertStringNotContainsString('broken-link', $expected);
            self::assertStringContainsString('/doc/api/2.0/yii-example-parenttype#run()-detail', $expected);

            $serialized = serialize(MarkdownContext::create($context));
            self::assertLessThan(strlen(serialize($context)) / 10, strlen($serialized));
            $renderer->apiContext = unserialize($serialized);

            self::assertSame($expected, ApiMarkdown::process($markdown));
            self::assertSame(str_repeat('Documentation that Markdown links do not need. ', 10000), $type->description);
            self::assertSame(str_repeat('// Method source code. ', 10000), $type->methods['run']->sourceCode);
        } finally {
            ApiMarkdown::$renderer = $originalRenderer;
        }
    }
}
