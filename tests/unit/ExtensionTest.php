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
}
