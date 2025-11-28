<?php

use app\migrations\BaseMigration;

class m251128_183910_user_indexes extends BaseMigration
{
    public function up()
    {
        $this->createIndex('idx-user-status', '{{%user}}', ['status']);
        $this->createIndex('idx-user-display_name', '{{%user}}', ['status', 'display_name']);
        $this->createIndex('idx-user-rating', '{{%user}}', ['status', 'rating']);
        $this->createIndex('idx-user-comment_count', '{{%user}}', ['status', 'comment_count']);
        $this->createIndex('idx-user-created_at', '{{%user}}', ['status', 'created_at']);
        $this->createIndex('idx-user-post_count', '{{%user}}', ['status',  'post_count']);
        $this->createIndex('idx-user-wiki_count', '{{%user}}', ['status', 'wiki_count']);
        $this->createIndex('idx-user-extension_count', '{{%user}}', ['status', 'extension_count']);
    }

    public function down()
    {
        $this->dropIndex('idx-user-status', '{{%user}}');
        $this->dropIndex('idx-user-display_name', '{{%user}}');
        $this->dropIndex('idx-user-rating', '{{%user}}');
        $this->dropIndex('idx-user-comment_count', '{{%user}}');
        $this->dropIndex('idx-user-created_at', '{{%user}}');
        $this->dropIndex('idx-user-post_count', '{{%user}}');
        $this->dropIndex('idx-user-wiki_count', '{{%user}}');
        $this->dropIndex('idx-user-extension_count', '{{%user}}');
    }
}
