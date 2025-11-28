<?php


use app\migrations\BaseMigration;

class m170114_212512_extension_tags_table extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%extension_tags}}', [
            'id' => $this->primaryKey(),
            'frequency' => $this->integer()->defaultValue(0),
            'name' => $this->string(128),
            'slug' => $this->string(128),
        ], $this->tableOptions);

        $this->createTable('{{%extension2extension_tags}}', [
            'extension_id' => $this->integer(),
            'extension_tag_id' => $this->integer(),
        ], $this->tableOptions);

        $this->addForeignKey('extension2extension_tags_extension', '{{%extension2extension_tags}}', 'extension_id', '{{%extension}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('extension2extension_tags_extension_tags', '{{%extension2extension_tags}}', 'extension_tag_id', '{{%extension_tags}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%extension2extension_tags}}');
        $this->dropTable('{{%extension_tags}}');
    }
}
