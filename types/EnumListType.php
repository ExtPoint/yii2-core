<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Enum;

class EnumListType extends EnumType
{
    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'DropDownField',
                'multiple' => true,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return 'varchar(255)[]';
    }

    public function giiRules($metaItem, &$useClasses = [])
    {
        /** @var Enum $className */
        $className = $metaItem->enumClassName;

        //TODO return "['in', 'range' => $className::getKeys()]";

        return [];
    }
}