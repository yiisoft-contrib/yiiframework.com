<?php

use app\migrations\BaseMigration;

class m160824_095813_create_news_tags_table extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%news_tags}}', [
            'id' => $this->primaryKey(),
            'frequency' => $this->integer()->defaultValue(0),
            'name' => $this->string(128),
            'slug' => $this->string(128),
        ], $this->tableOptions);

        $this->createTable('{{%news2news_tags}}', [
            'news_id' => $this->integer(),
            'news_tag_id' => $this->integer(),
        ], $this->tableOptions);

        $this->addForeignKey('news2news_tags_news', '{{%news2news_tags}}', 'news_id', '{{%news}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('news2news_tags_news_tags', '{{%news2news_tags}}', 'news_tag_id', '{{%news_tags}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%news2news_tags}}');
        $this->dropTable('{{%news_tags}}');
    }
}
