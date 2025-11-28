<?php

use app\migrations\BaseMigration;

class m190331_122553_add_avatar_version extends BaseMigration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'avatar_version', $this->integer()->notNull()->defaultValue(1)->after('email'));
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'avatar_version');
    }
}
