<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use extpoint\yii2\gii\models\EnumClass;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

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
    public function getGiiJsMetaItem($metaItem, $item, &$import = [])
    {
        $result = parent::getGiiJsMetaItem($metaItem, $item, $import);
        if ($metaItem->enumClassName) {
            $enumClassMeta = EnumClass::findOne($metaItem->enumClassName);
            if (file_exists($enumClassMeta->metaClass->filePath)) {
                $import[] = 'import ' . $enumClassMeta->metaClass->name . ' from \'' . str_replace('\\', '/', $enumClassMeta->metaClass->className) . '\';';
                $result['enumClassName'] = new JsExpression($enumClassMeta->metaClass->name);
            } elseif (file_exists($enumClassMeta->filePath)) {
                $import[] = 'import ' . $enumClassMeta->name . ' from \'' . str_replace('\\', '/', $enumClassMeta->className) . '\';';
                $result['enumClassName'] = new JsExpression($enumClassMeta->name);
            }
        }
        return $result;
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