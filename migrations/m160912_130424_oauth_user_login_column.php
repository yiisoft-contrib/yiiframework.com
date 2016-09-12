<?php

use yii\db\Migration;

class m160912_130424_oauth_user_login_column extends Migration
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
