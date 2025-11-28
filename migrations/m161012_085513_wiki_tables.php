<?php

use app\migrations\BaseMigration;

class m161012_085513_wiki_tables extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%wiki_categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'sequence' => $this->smallInteger(),
        ], $this->tableOptions);

        $this->createTable('{{%wiki}}', [
            'id' => $this->primaryKey(),

            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'content' => $this->text()->notNull(),

            'category_id' => $this->integer()->notNull(),

            'creator_id' => $this->integer()->notNull(),
            'updater_id' => $this->integer(),

            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),

            'total_votes' => $this->integer()->notNull()->defaultValue(0),
            'up_votes' => $this->integer()->notNull()->defaultValue(0),
            'rating' => $this->double()->notNull()->defaultValue(0),
            'featured' => $this->boolean()->notNull()->defaultValue(0),

            'comment_count' => $this->integer()->notNull()->defaultValue(0),
            'view_count' => $this->integer()->notNull()->defaultValue(0),

            'yii_version' => $this->string(5),

            'status' => $this->smallInteger()->notNull()->defaultValue(3), // published

        ], $this->tableOptions);

        $this->addForeignKey('fk-wiki-category_id-wiki_category-id', '{{%wiki}}', 'category_id', '{{%wiki_categories}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-wiki-creator_id-user-id', '{{%wiki}}', 'creator_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-wiki-updater_id-user-id', '{{%wiki}}', 'updater_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');


        $this->createTable('{{%wiki_revision}}', [
            'wiki_id' => $this->integer()->notNull(),
            'revision' => $this->integer()->notNull(),

            'title' => $this->string(255)->notNull(),
            'content' => $this->text()->notNull(),

            'tagNames' => $this->string(255),

            'category_id' => $this->integer()->notNull(),
            'yii_version' => $this->string(5),

            // note about what has changed
            'memo' => $this->string(255),

            'updater_id' => $this->integer(),
            'updated_at' => $this->dateTime(),

            'PRIMARY KEY (`wiki_id`,`revision`)',

        ], $this->tableOptions);
        $this->addForeignKey('fk-wiki_revision-wiki_id-wiki-id', '{{%wiki_revision}}', 'wiki_id', '{{%wiki}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-wiki_revision-updater_id-user-id', '{{%wiki_revision}}', 'updater_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('{{%wiki_revision}}');
        $this->dropTable('{{%wiki}}');
        $this->dropTable('{{%wiki_categories}}');
    }

}
