<?php

use app\migrations\BaseMigration;

class m180202_111933_create_table_doc extends BaseMigration
{
    public function safeUp()
    {
        $this->createTable('{{%doc}}', [
            'id' => $this->primaryKey(),
            'object_type' => $this->string()->notNull(),
            'object_key' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),
            'title' => $this->string(),
        ], $this->tableOptions);

        $this->createIndex('idx-doc-object_type-object_key-unique', '{{%doc}}', ['object_type', 'object_key'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%doc}}');
    }
}
