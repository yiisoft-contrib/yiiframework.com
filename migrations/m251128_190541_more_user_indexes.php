<?php

use app\migrations\BaseMigration;

class m251128_190541_more_user_indexes extends BaseMigration
{
    public function up()
    {
        $this->createIndex('idx-user-rank', '{{%user}}', ['status', 'rank']);

    }

    public function down()
    {
        $this->dropIndex('idx-user-rank', '{{%user}}');
    }
}
