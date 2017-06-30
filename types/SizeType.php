<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;

class SizeType extends Type
{
    public $formatter = 'shortSize';

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'number'],
        ];
    }
}