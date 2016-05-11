<?php

use app\migrations\BaseMigration;
use yii\db\Migration;

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

	        // `tags` text, // TODO

	        'status' => $this->integer(),
        ], $this->tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%news}}');
    }
}
