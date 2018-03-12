<?php

use app\migrations\BaseMigration;

class m180312_100529_add_forum_id_to_user extends BaseMigration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'forum_id', $this->integer()->unique());
        $this->update('{{%user}}', ['forum_id' => new \yii\db\Expression('id')]);

    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'forum_id');
    }
}
