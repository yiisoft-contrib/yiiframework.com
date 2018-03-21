<?php

use app\migrations\BaseMigration;
use yii\db\Expression;

class m180319_145940_content_share__change_type_time extends BaseMigration
{
    public function safeUp()
    {

        $this->addColumn('{{%content_share}}', 'created_at_new', $this->dateTime()->null());
        $this->update('{{%content_share}}', ['created_at_new' => new Expression('FROM_UNIXTIME(created_at)')]);
        $this->dropColumn('{{%content_share}}', 'created_at');
        $this->renameColumn('{{%content_share}}', 'created_at_new', 'created_at');
        $this->alterColumn('{{%content_share}}', 'created_at', $this->dateTime()->notNull());

        $this->addColumn('{{%content_share}}', 'posted_at_new', $this->dateTime()->null());
        $this->update('{{%content_share}}', ['posted_at_new' => new Expression('FROM_UNIXTIME(posted_at)')]);
        $this->dropColumn('{{%content_share}}', 'posted_at');
        $this->renameColumn('{{%content_share}}', 'posted_at_new', 'posted_at');
    }

    public function safeDown()
    {
        $this->addColumn('{{%content_share}}', 'created_at_new', $this->integer()->null());
        $this->update('{{%content_share}}', ['created_at_new' => new Expression('UNIX_TIMESTAMP(created_at)')]);
        $this->dropColumn('{{%content_share}}', 'created_at');
        $this->renameColumn('{{%content_share}}', 'created_at_new', 'created_at');
        $this->alterColumn('{{%content_share}}', 'created_at', $this->integer()->notNull());

        $this->addColumn('{{%content_share}}', 'posted_at_new', $this->integer()->null());
        $this->update('{{%content_share}}', ['posted_at_new' => new Expression('UNIX_TIMESTAMP(posted_at)')]);
        $this->dropColumn('{{%content_share}}', 'posted_at');
        $this->renameColumn('{{%content_share}}', 'posted_at_new', 'posted_at');
    }
}
