<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\db\Schema;

class PrimaryKeyType extends Type
{
    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return Schema::TYPE_PK;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return false;
    }
}