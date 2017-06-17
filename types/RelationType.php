<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\ArrayType;
use yii\db\Schema;

class RelationType extends ArrayType
{
    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem)
    {
        $relation = $metaItem->metaClass->getRelation($metaItem->relationName);
        return $relation && $relation->isHasOne ? Schema::TYPE_INTEGER : false;
    }

    /**
     * @inheritdoc
     */
    public function getGiiBehaviors($metaItem)
    {
        return !$this->getGiiDbType($metaItem) ? parent::getGiiBehaviors($metaItem) : [];
    }

    /**
     * @inheritdoc
     */
    public function getGiiRules($metaItem, &$useClasses = [])
    {
        $relation = $metaItem->metaClass->getRelation($metaItem->relationName);
        if (!$relation) {
            return false;
        }
        return [
            $relation->isHasOne
                ? [$metaItem->name, 'integer']
                : [$metaItem->name, 'each', 'rule' => ['integer']]
        ];
    }
}