<?php

use app\migrations\BaseMigration;

class m180403_165936_extension_version_references extends BaseMigration
{
    public function up()
    {
        $this->addColumn('{{%extension}}', 'version_references', 'string DEFAULT NULL AFTER yii_version');
    }

    public function down()
    {
        $this->dropColumn('{{%extension}}', 'version_references');
    }
}
