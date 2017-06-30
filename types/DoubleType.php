<?php

namespace extpoint\yii2\types;

use yii\db\Schema;

class DoubleType extends IntegerType
{
    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_DOUBLE;
    }
}