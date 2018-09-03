<?php

use app\migrations\BaseMigration;

/**
 * Queue jobs may be larger than BLOB which has a limit of 65536 (2^16) bytes.
 */
class m180903_114713_disable_forum_badges extends BaseMigration
{
    public function up()
    {
        $this->addColumn('{{%badges}}', 'active', 'BOOLEAN NOT NULL DEFAULT 1');
        $this->update('{{%badges}}', ['active' => 0], "class LIKE 'ForumPost%'");
    }

    public function down()
    {
        $this->dropColumn('{{%badges}}', 'active');
    }
}
