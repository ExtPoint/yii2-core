<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Enum;
use extpoint\yii2\base\Type;
use extpoint\yii2\gii\models\EnumClass;
use extpoint\yii2\gii\models\ValueExpression;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use extpoint\yii2\gii\helpers\GiiHelper;
use yii\helpers\StringHelper;

class EnumType extends Type
{
    const OPTION_CLASS_NAME = 'enumClassName';

    /**
     * @inheritdoc
     */
    public function renderField($model, $attribute, $item, $options = [])
    {
        /** @var Enum $className */
        $className = ArrayHelper::getValue($item, self::OPTION_CLASS_NAME);
        $items = $className::getLabels();

        if ($this->inputWidget) {
            return $this->renderInputWidget($item, [
                'model' => $model,
                'attribute' => $attribute,
                'items' => $items,
                'options' => $options,
            ]);
        }

        return Html::activeDropDownList($model, $attribute, $items, array_merge(['class' => 'form-control'], $options));
    }

    /**
     * @inheritdoc
     */
    public function renderFormField($field, $item, $options = [])
    {
        /** @var Enum $className */
        $className = ArrayHelper::getValue($item, self::OPTION_CLASS_NAME);
        $items = $className::getLabels();

        if ($this->inputWidget) {
            return $this->renderInputWidget($item, [
                'field' => $field,
                'options' => $options,
                'items' => $items,
            ]);
        }

        return $this->renderField($field->model, $field->attribute, $item, $options);
    }

    /**
     * @inheritdoc
     */
    public function renderSearchField($model, $attribute, $item, $options = []) {
        /** @var Enum $className */
        $className = ArrayHelper::getValue($item, self::OPTION_CLASS_NAME);

        $items = ['' => ''] + $className::getLabels();
        return Html::activeDropDownList($model, $attribute, $items, array_merge(['class' => 'form-control'], $options));
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = [])
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
    public function getGiiDbType($metaItem)
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function getGiiRules($metaItem, &$useClasses = [])
    {
        /** @var Enum $className */
        $className = $metaItem->enumClassName;
        $shortClassName = StringHelper::basename($metaItem->enumClassName);

        $useClasses[] = $className;

        return [
            [$metaItem->name, 'in', 'range' => new ValueExpression("$shortClassName::getKeys()")],
        ];
    }

    /**
     * @return array
     */
    public function getGiiFieldProps()
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