<?php

use app\migrations\BaseMigration;
use yii\db\Schema;

class m150416_155549_create_user_and_auth_tables extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(64)->notNull(),
            'display_name' => $this->string(64)->notNull(),
            'email' => $this->string(255)->notNull(),

            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(128)->notNull(),
            'password_reset_token' => $this->string(),

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),

            'login_time' => $this->dateTime(),
            'login_attempts' => $this->integer()->notNull()->defaultValue(0),
            'login_ip' => $this->string(39),

        ], $this->tableOptions);
        $this->createIndex('idx-user-username-unique', '{{%user}}', 'username', true);
        $this->createIndex('idx-user-email-unique', '{{%user}}', 'email', true);

        $this->createTable('{{%auth}}', [
            'id' => $this->primaryKey(),
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'source' => Schema::TYPE_STRING . ' NOT NULL',
            'source_id' => $this->string(16)->notNull(),
        ], $this->tableOptions);

        $this->addForeignKey('fk-auth-user_id-user-id', '{{%auth}}', 'user_id', '{{%user}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%auth}}');
        $this->dropTable('{{%user}}');
    }
}
