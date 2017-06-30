<?php

namespace extpoint\yii2\types;

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

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = [])
    {
        /** @var Enum $className */
        $className = $metaItem->enumClassName;

        //TODO return "['in', 'range' => $className::getKeys()]";
    }
}