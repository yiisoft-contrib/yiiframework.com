<?php

use app\migrations\BaseMigration;

class m160912_130424_oauth_user_login_column extends BaseMigration
{
    public function up()
    {
        $this->addColumn('{{%auth}}', 'source_login', $this->string(128));
    }

    public function down()
    {
        $this->dropColumn('{{%auth}}', 'source_login');
    }
}
