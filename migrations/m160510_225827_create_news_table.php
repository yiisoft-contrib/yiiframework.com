<?php

use app\migrations\BaseMigration;

class m160510_225827_create_news_table extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(128)->notNull(),
            'slug' => $this->string(128),
            'news_date' => $this->date(),
            'image_id' => $this->integer(), // TODO
            'content' => $this->text(),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),

			'creator_id' => $this->integer(),
			'updater_id' => $this->integer(),

	        'status' => $this->integer(),
        ], $this->tableOptions);

        $this->addForeignKey('fk-news-creator_id-user-id', '{{%news}}', 'creator_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-news-updater_id-user-id', '{{%news}}', 'updater_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('{{%news}}');
    }
}
