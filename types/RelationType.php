<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\ArrayType;
use yii\db\Schema;

class RelationType extends ArrayType
{
    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        $relation = $metaItem->metaClass->getRelation($metaItem->relationName);
        return $relation && $relation->isHasOne ? Schema::TYPE_INTEGER : false;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        $relation = $metaItem->metaClass->getRelation($metaItem->relationName);
        return $relation && $relation->isHasOne ? 'integer' : false;
    }
}