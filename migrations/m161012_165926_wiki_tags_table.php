<?php


use app\migrations\BaseMigration;

class m161012_165926_wiki_tags_table extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%wiki_tags}}', [
            'id' => $this->primaryKey(),
            'frequency' => $this->integer()->defaultValue(0),
            'name' => $this->string(128),
            'slug' => $this->string(128),
        ], $this->tableOptions);

        $this->createTable('{{%wiki2wiki_tags}}', [
            'wiki_id' => $this->integer(),
            'wiki_tag_id' => $this->integer(),
        ], $this->tableOptions);

        $this->addForeignKey('wiki2wiki_tags_wiki', '{{%wiki2wiki_tags}}', 'wiki_id', '{{%wiki}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('wiki2wiki_tags_wiki_tags', '{{%wiki2wiki_tags}}', 'wiki_tag_id', '{{%wiki_tags}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%wiki2wiki_tags}}');
        $this->dropTable('{{%wiki_tags}}');
    }
}
