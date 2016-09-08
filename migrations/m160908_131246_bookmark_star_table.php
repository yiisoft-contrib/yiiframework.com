<?php

use app\migrations\BaseMigration;

class m160908_131246_bookmark_star_table extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%star}}', [
            'user_id' => $this->integer()->notNull(),
            'object_type' => $this->string(128)->notNull(),
            'object_id' => $this->integer()->notNull(),
            'star' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->notNull(),
            'PRIMARY KEY(user_id, object_type, object_id)'
        ], $this->tableOptions);
        $this->addForeignKey('fk-star-user_id-user-id', '{{%star}}', 'user_id', '{{%user}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%star}}');
    }
}
