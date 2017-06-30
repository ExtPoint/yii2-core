<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use extpoint\yii2\gii\models\MetaItem;
use yii\db\Schema;

class EmailType extends Type
{
    public $formatter = 'email';

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'EmailField',
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
        return [
            [$metaItem->name, 'string', 'max' => 255],
            [$metaItem->name, 'email'],
        ];
    }
}