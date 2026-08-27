<?php

use app\components\packagist\PackagistApi;

class PackagistApiTest extends \Codeception\Test\Unit
{
    public function testBuildsGithubRawRepositoryUrl(): void
    {
        self::assertSame(
            'https://raw.githubusercontent.com/yiisoft/yii2-bootstrap4/master',
            PackagistApi::getRawRepositoryUrl('https://github.com/yiisoft/yii2-bootstrap4.git')
        );
    }
}
