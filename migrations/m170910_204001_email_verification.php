<?php

use app\migrations\BaseMigration;

class m170910_204001_email_verification extends BaseMigration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'email_verification_token', $this->string()->unique());
        $this->addColumn('{{%user}}', 'email_verified', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'email_verification_token');
        $this->dropColumn('{{%user}}', 'email_verified');
    }
}
