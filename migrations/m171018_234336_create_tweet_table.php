<?php

use app\migrations\BaseMigration;

/**
 * Handles the creation of table `tweet`.
 */
class m171018_234336_create_tweet_table extends BaseMigration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tweet', [
            'id' => $this->primaryKey(),
            'object_type' => $this->string()->notNull(),
            'object_id' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'posted_at' => $this->integer(),
            'message' => $this->string(),
        ], $this->tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tweet');
    }
}
