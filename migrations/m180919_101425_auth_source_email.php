<?php

use app\migrations\BaseMigration;

class m180919_101425_auth_source_email extends BaseMigration
{
    public function safeUp()
    {
        $this->addColumn('{{%auth}}', 'source_email', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%auth}}', 'source_email');
    }
}
