<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use extpoint\yii2\gii\models\EnumClass;
use yii\helpers\ArrayHelper;

class CategorizedStringType extends Type
{
    const OPTION_ENUM_CLASS = 'enumClassName';
    const OPTION_REF_ATTRIBUTE = 'refAttribute';

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'CategorizedStringField',
                'refAttributeOptions' => [
                    self::OPTION_REF_ATTRIBUTE,
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            self::OPTION_REF_ATTRIBUTE => [
                'component' => 'input',
                'label' => 'Category Attribute',
                'list' => 'attributes',
                'style' => [
                    'width' => '80px'
                ]
            ],
            self::OPTION_ENUM_CLASS => [
                'component' => 'input',
                'label' => 'Class',
                'list' => ArrayHelper::getColumn(EnumClass::findAll(), 'className'),
                'style' => [
                    'width' => '80px'
                ]
            ],
        ];
    }
}