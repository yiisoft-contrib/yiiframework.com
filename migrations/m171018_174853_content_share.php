<?php

use app\migrations\BaseMigration;
use app\models\ContentShare;

class m171018_174853_content_share extends BaseMigration
{
    public function safeUp()
    {
        $this->createTable('{{%content_share}}', [
            'object_type_id' => $this->smallInteger()->notNull(),
            'object_id' => $this->integer()->notNull(),
            'service_id' => $this->smallInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'PRIMARY KEY(object_type_id, object_id, service_id)'
        ], $this->tableOptions);

        foreach (ContentShare::$objectTypesData as $objectTypeId => $objectTypeData) {
            /** @var string $tableName */
            $tableName = $objectTypeData['className']::tableName();
            foreach (ContentShare::$availableServiceIds as $serviceId) {
                $sql = "INSERT IGNORE content_share (object_type_id, object_id, service_id, created_at) 
                    (SELECT :object_type_id, id, :service_id, UNIX_TIMESTAMP() FROM {$tableName} 
                     WHERE [[{$objectTypeData['objectStatusPropertyName']}]] = :objectStatusPublishedId)";

                $this->execute($sql, [
                    'object_type_id' => $objectTypeId,
                    'service_id' => $serviceId,
                    'objectStatusPublishedId' => $objectTypeData['objectStatusPublishedId']
                ]);
            }
        }
    }

    public function safeDown()
    {
        $this->dropTable('{{%content_share}}');
    }
}
