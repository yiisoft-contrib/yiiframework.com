<?php

use app\migrations\BaseMigration;

class m160825_210900_add_user_ranking_system extends BaseMigration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'rating', 'integer NOT NULL DEFAULT 0');
        $this->addColumn('{{%user}}', 'rank', 'integer NOT NULL DEFAULT 999999');
        $this->addColumn('{{%user}}', 'extension_count', 'integer NOT NULL DEFAULT 0');
        $this->addColumn('{{%user}}', 'wiki_count', 'integer NOT NULL DEFAULT 0');
        $this->addColumn('{{%user}}', 'comment_count', 'integer NOT NULL DEFAULT 0');
        $this->addColumn('{{%user}}', 'post_count', 'integer NOT NULL DEFAULT 0');


        $this->createTable('{{%badges}}', [
            'id' => $this->primaryKey(),
            'urlname' => $this->string(255)->notNull(),
            'class' => $this->string(50)->notNull(),
            'achieved' => $this->integer()->notNull()->defaultValue(0),
        ], $this->tableOptions);

        $this->createTable('{{%badge_queue}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
        ], $this->tableOptions);
        $this->addForeignKey('fk-badge_queue-user_id-user-id', '{{%badge_queue}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%user_badges}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'badge_id' => $this->integer()->notNull(),
            'progress' => $this->smallInteger()->notNull()->defaultValue(0),
            'create_time' => $this->dateTime()->notNull(),
            'complete_time' => $this->dateTime(),
            'message' => $this->string(),
            'notified' => $this->boolean()->notNull()->defaultValue(0),
        ], $this->tableOptions);
        $this->addForeignKey('fk-user_badges-user_id-user-id', '{{%user_badges}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-user_badges-badge_id-bages-id', '{{%user_badges}}', 'badge_id', '{{%badges}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'rating');
        $this->dropColumn('{{%user}}', 'rank');
        $this->dropColumn('{{%user}}', 'extension_count');
        $this->dropColumn('{{%user}}', 'wiki_count');
        $this->dropColumn('{{%user}}', 'comment_count');
        $this->dropColumn('{{%user}}', 'post_count');

        $this->dropTable('{{%user_badges}}');
        $this->dropTable('{{%badge_queue}}');
        $this->dropTable('{{%badges}}');
    }
}
