<?php

use app\models\Extension;

class ExtensionTest extends \Codeception\Test\Unit
{
    public function testLicenseLinkHandlesMissingLicense(): void
    {
        $extension = new class extends Extension {
            public $license_id;
        };
        $extension->license_id = null;

        self::assertSame(Yii::$app->formatter->nullDisplay, $extension->getLicenseLink());
    }

    public function testNormalizesLegacyBootstrap4Images(): void
    {
        $extension = new class extends Extension {
            public function normalizeMarkdown($markdown)
            {
                return $this->normalizeImportedMarkdown($markdown);
            }
        };

        $markdown = implode("\n", [
            '![Bootstrap](https://v4-alpha.getbootstrap.com/assets/brand/bootstrap-solid.svg)',
            '![Build](https://github.com/yiisoft/yii2-bootstrap4/workflows/build/badge.svg)',
        ]);
        $normalized = $extension->normalizeMarkdown($markdown);

        self::assertStringContainsString(
            'https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg',
            $normalized
        );
        self::assertStringContainsString(
            'https://img.shields.io/github/actions/workflow/status/yiisoft/yii2-bootstrap4/build.yml?style=for-the-badge&logo=github&label=Build',
            $normalized
        );
    }

}
