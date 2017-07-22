<?php

use app\migrations\BaseMigration;

class m170719_154949_files_table extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'object_type' => $this->string()->notNull(),
            'object_id' => $this->integer()->notNull(),
            'file_name' => $this->string()->notNull(),
            'file_size' => $this->integer()->notNull(),
            'mime_type' => $this->string()->notNull(),
            'download_count' => $this->integer()->notNull()->defaultValue(0),
            'summary' => $this->string(),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
        ], $this->tableOptions);

        $this->addForeignKey('file_created_by', '{{%file}}', 'created_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%file}}');
    }
}
