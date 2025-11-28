<?php

use app\migrations\BaseMigration;

class m171018_174853_content_share extends BaseMigration
{
    public function safeUp()
    {
        $this->createTable('{{%content_share}}', [
            'id' => $this->primaryKey(),
            'object_type_id' => $this->string()->notNull(),
            'object_id' => $this->integer()->notNull(),
            'service_id' => $this->string()->notNull(),
            'status_id' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'posted_at' => $this->integer(),
            'message' => $this->text()->notNull()
        ], $this->tableOptions);

        $this->createIndex('idx-content_share-object_type_id-object_id-service_id-unique', '{{%content_share}}', ['object_type_id', 'object_id', 'service_id'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%content_share}}');
    }
}
