<?php

use app\migrations\BaseMigration;

class m180625_224112_extension_version_references_fix extends BaseMigration
{
    public function up()
    {
        $this->alterColumn('{{%extension}}', 'version_references', 'text DEFAULT NULL');
    }

    public function down()
    {
        $this->alterColumn('{{%extension}}', 'version_references', 'string DEFAULT NULL');
    }
}
