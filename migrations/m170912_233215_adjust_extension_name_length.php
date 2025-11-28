<?php

use app\migrations\BaseMigration;

class m170912_233215_adjust_extension_name_length extends BaseMigration
{
    public function safeUp()
    {
        $this->alterColumn('{{%extension}}', 'name', $this->string()->notNull());
    }

    public function safeDown()
    {
        $this->alterColumn('{{%extension}}', 'name', $this->string(32)->notNull());
    }
}
