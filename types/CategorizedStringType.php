<?php

namespace extpoint\yii2\types;

class CategorizedStringType extends EnumType
{
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
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return array_merge(
            parent::giiOptions(),
            [
                self::OPTION_REF_ATTRIBUTE => [
                    'component' => 'input',
                    'label' => 'Category Attribute',
                    'list' => 'attributes',
                    'style' => [
                        'width' => '80px'
                    ]
                ],
            ]
        );
    }
}