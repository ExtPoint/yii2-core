<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use extpoint\yii2\gii\models\MetaItem;
use yii\db\Schema;

class PhoneType extends Type
{
    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'PhoneField',
            ]
        ];
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        // TODO Phone validator
        return [
            [$metaItem->name, 'string', 'max' => $metaItem->stringLength ?: 255],
        ];
    }
}