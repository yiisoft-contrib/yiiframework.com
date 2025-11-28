<?php

use app\migrations\BaseMigration;

class m180316_145220_comment_user_id_null extends BaseMigration
{
    public function up()
    {
        $this->alterColumn('{{%comment}}', 'user_id', $this->integer()->null());
    }

    public function down()
    {
        $this->alterColumn('{{%comment}}', 'user_id', $this->integer()->notNull());
    }
}
