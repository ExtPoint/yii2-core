<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Enum;
use extpoint\yii2\base\Type;
use extpoint\yii2\gii\models\EnumClass;
use extpoint\yii2\gii\models\ValueExpression;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\JsExpression;

class EnumType extends Type
{
    const OPTION_CLASS_NAME = 'enumClassName';

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'DropDownField',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderInputWidget($item, $class, $config)
    {
        /** @var Enum $className */
        $className = ArrayHelper::getValue($item, self::OPTION_CLASS_NAME);
        $config['options']['items'] = $className::getLabels();

        return $class::widget($config);
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        /** @var Enum $className */
        $className = ArrayHelper::getValue($item, self::OPTION_CLASS_NAME);

        $label = $className::getLabel($model->$attribute);
        $cssClass = $className::getCssClass($model->$attribute);

        return $cssClass ? Html::tag('span', $label, ['class' => $cssClass]) : $label;
    }

    /**
     * @inheritdoc
     */
    public function getGiiJsMetaItem($metaItem, $item, &$import = [])
    {
        $result = parent::getGiiJsMetaItem($metaItem, $item, $import);
        if ($metaItem->enumClassName) {
            $enumClassMeta = EnumClass::findOne($metaItem->enumClassName);
            if (file_exists($enumClassMeta->metaClass->jsFilePath)) {
                $import[] = 'import ' . $enumClassMeta->metaClass->name . ' from \'' . str_replace('\\', '/', $enumClassMeta->metaClass->className) . '\';';
            }
            $result['enumClassName'] = new JsExpression($enumClassMeta->metaClass->name);
        }
        return $result;
    }

    /**
     * @inheritdoc
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
        /** @var Enum $className */
        $className = $metaItem->enumClassName;
        if (!$className) {
            return [
                [$metaItem->name, 'string'],
            ];
        }

        $shortClassName = StringHelper::basename($metaItem->enumClassName);
        $useClasses[] = $className;

        return [
            [$metaItem->name, 'in', 'range' => new ValueExpression("$shortClassName::getKeys()")],
        ];
    }

    /**
     * @return array
     */
    public function giiOptions()
    {
        return [
            self::OPTION_CLASS_NAME => [
                'component' => 'input',
                'label' => 'Class',
                'list' => ArrayHelper::getColumn(EnumClass::findAll(), 'className'),
            ]
        ];
    }
}