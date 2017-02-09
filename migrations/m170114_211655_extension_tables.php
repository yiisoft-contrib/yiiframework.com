<?php

use app\migrations\BaseMigration;

class m170114_211655_extension_tables extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%extension_categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'sequence' => $this->smallInteger(),
        ], $this->tableOptions);

        $this->createTable('{{%extension}}', [
            'id' => $this->primaryKey(),

            'name' => $this->string(32)->notNull(),
            'tagline' => $this->string(128)->notNull(),

            'category_id' => $this->integer()->notNull(),
            'license_id' => $this->integer()->notNull(),

            // TODO is packagist or not?

            'owner_id' => $this->integer()->notNull(),

            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),

            'total_votes' => $this->integer()->notNull()->defaultValue(0),
            'up_votes' => $this->integer()->notNull()->defaultValue(0),
            'rating' => $this->double()->notNull()->defaultValue(0),
            'featured' => $this->boolean()->notNull()->defaultValue(0),

            'comment_count' => $this->integer()->notNull()->defaultValue(0),
            'download_count' => $this->integer()->notNull()->defaultValue(0),

            'yii_version' => $this->string(5),

            'status' => $this->smallInteger()->notNull()->defaultValue(3), // published

            'description' => $this->text(),

        ], $this->tableOptions);

        $this->addForeignKey('fk-extension-category_id-extension_category-id', '{{%extension}}', 'category_id', '{{%extension_categories}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-extension-owner_id-user-id', '{{%extension}}', 'owner_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%extension}}');
        $this->dropTable('{{%extension_categories}}');
    }

}
