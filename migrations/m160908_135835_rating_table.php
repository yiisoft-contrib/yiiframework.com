<?php

use app\migrations\BaseMigration;

class m160908_135835_rating_table extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%rating}}', [
            'user_id' => $this->integer()->notNull(),
            'object_type' => $this->string(128)->notNull(),
            'object_id' => $this->integer()->notNull(),
            'rating' => $this->smallInteger()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'PRIMARY KEY(user_id, object_type, object_id)'
        ], $this->tableOptions);
        $this->addForeignKey('fk-rating-user_id-user-id', '{{%rating}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');

        $this->addColumn('{{%comment}}', 'total_votes', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%comment}}', 'up_votes', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%comment}}', 'rating', $this->double()->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('{{%comment}}', 'rating');
        $this->dropColumn('{{%comment}}', 'up_votes');
        $this->dropColumn('{{%comment}}', 'total_votes');
        $this->dropTable('{{%rating}}');
    }
}
