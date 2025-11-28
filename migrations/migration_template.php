<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name */

echo "<?php\n";
?>

use app\migrations\BaseMigration;

class <?= $className ?> extends BaseMigration
{
    public function up()
    {
        // note: when creating tables using $this->createTable(), pass $this->tableOptions as last parameter

    }

    public function down()
    {
        echo "<?= $className ?> cannot be reverted.\n";
        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
