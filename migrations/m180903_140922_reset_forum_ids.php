<?php

use app\migrations\BaseMigration;

/**
 * migrating to Discourse, reset all forum_id entries to be re-filled with discourse forum ids
 */
class m180903_140922_reset_forum_ids extends BaseMigration
{
    public function up()
    {
        $this->update('{{%user}}', ['forum_id' => null]);
    }

    public function down()
    {
    }
}
