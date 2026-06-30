<?php

use yii\db\Migration;

/**
 * Class m260418_090000_convert_tables_to_utf8mb4
 */
class m260418_090000_convert_tables_to_utf8mb4 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Change database default charset
        $this->execute('ALTER DATABASE CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;');

        // Get all tables
        $tables = $this->db->schema->getTableNames();

        foreach ($tables as $table) {
            $this->execute("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Revert database default charset
        $this->execute('ALTER DATABASE CHARACTER SET = utf8 COLLATE = utf8_general_ci;');

        // Get all tables
        $tables = $this->db->schema->getTableNames();

        foreach ($tables as $table) {
            $this->execute("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
        }
    }
}
