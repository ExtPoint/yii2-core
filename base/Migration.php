<?php

namespace extpoint\yii2\base;

class Migration extends \yii\db\Migration
{
    /**
     * @inheritdoc
     */
    public function createTable($table, $columns, $options = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB')
    {
        return parent::createTable($table, $columns, $options);
    }

    /**
     * @param string $tableName
     * @param string $from
     * @param string $to
     * @param array $columns
     */
    public function createJunctionTable($tableName, $from, $to, $columns = [])
    {
        $this->createTable($tableName, array_merge(
            [
                $from => $this->integer()->notNull(),
                $to => $this->integer()->notNull()
            ],
            $columns
        ));
        $this->addPrimaryKey(sprintf('%s_pk', $tableName), $tableName, [$from, $to]);
    }

}
