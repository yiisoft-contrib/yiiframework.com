<?php

use app\migrations\BaseMigration;
use yii\db\Schema;

class m150416_155923_create_comment_table extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'object_type' => $this->string(255)->notNull(),
            'object_id' => $this->string(255)->notNull(),
            'text' => $this->text()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
        ], $this->tableOptions);

        $this->addForeignKey('fk-comment-user_id-user-id', '{{%comment}}', 'user_id', '{{%user}}', 'id', 'RESTRICT');
        $this->createIndex('idx-comment-object_type-object_id', '{{%comment}}', ['object_type', 'object_id']);
    }

    public function down()
    {
        $this->dropTable('{{%comment}}');
    }
}
