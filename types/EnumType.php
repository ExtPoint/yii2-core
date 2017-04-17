<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Enum;
use extpoint\yii2\base\Type;
use extpoint\yii2\gii\models\EnumClass;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use extpoint\yii2\gii\helpers\GiiHelper;

class EnumType extends Type
{
    const OPTION_CLASS_NAME = 'enumClassName';

    /**
     * @inheritdoc
     */
    public function renderField($field, $options = []) {
        /** @var Enum $className */
        $className = ArrayHelper::getValue($options, self::OPTION_CLASS_NAME);

        $field->dropDownList($className::getLabels(), $options);
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $options = []) {
        /** @var Enum $className */
        $className = ArrayHelper::getValue($options, self::OPTION_CLASS_NAME);

        $label = $className::getLabel($model->$attribute);
        $cssClass = $className::getCssClass($model->$attribute);

        return $cssClass ? Html::tag('span', $label, ['class' => $cssClass]) : $label;
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        /** @var Enum $className */
        $className = $metaItem->enumClassName;

        return "['in', 'range' => $className::getKeys()]";
    }

    /**
     * @return array
     */
    public function getGiiFieldProps() {
        return [
            self::OPTION_CLASS_NAME => [
                'component' => 'input',
                'label' => 'Class',
                'list' => ArrayHelper::getColumn(EnumClass::findAll(), 'className'),
            ]
        ];
    }
}