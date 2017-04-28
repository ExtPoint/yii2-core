<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\bootstrap\Html;
use yii\db\Schema;

class DoubleType extends IntegerType
{
    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem)
    {
        return Schema::TYPE_DOUBLE;
    }
}