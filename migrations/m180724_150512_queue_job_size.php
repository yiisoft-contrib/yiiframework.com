<?php

use app\migrations\BaseMigration;

/**
 * Queue jobs may be larger than BLOB which has a limit of 65536 (2^16) bytes.
 */
class m180724_150512_queue_job_size extends BaseMigration
{
    public function up()
    {
        $this->alterColumn('{{%queue}}', 'job', 'LONGBLOB NOT NULL');
    }

    public function down()
    {
        $this->alterColumn('{{%queue}}', 'job', $this->binary()->notNull());
    }
}
