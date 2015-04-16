<?php

use yii\db\Schema;
use yii\db\Migration;

class m150416_155549_create_user_and_auth_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING,

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        $this->createIndex('idx-user-username-unique', '{{%user}}', 'username', true);
        $this->createIndex('idx-user-email-unique', '{{%user}}', 'email', true);

        $this->createTable('{{%auth}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'source' => Schema::TYPE_STRING . ' NOT NULL',
            'source_id' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey('fk-auth-user_id-user-id', '{{%auth}}', 'user_id', '{{%user}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%auth}}');
        $this->dropTable('{{%user}}');
    }
}
