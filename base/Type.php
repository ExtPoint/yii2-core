<?php

namespace extpoint\yii2\base;

use extpoint\yii2\gii\models\MetaItem;
use extpoint\yii2\widgets\ActiveField;
use yii\base\Object;
use yii\db\Schema;
use yii\helpers\Html;
use yii\widgets\InputWidget;

abstract class Type extends Object
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var InputWidget
     */
    public $inputWidget;

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderField($model, $attribute, $item, $options = [])
    {
        if ($this->inputWidget) {
            return $this->renderInputWidget($item, [
                'model' => $model,
                'attribute' => $attribute,
                'options' => $options,
            ]);
        }

        return Html::activeTextInput($model, $attribute, array_merge(['class' => 'form-control'], $options));
    }

    /**
     * @param ActiveField $field
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderFormField($field, $item, $options = [])
    {
        if ($this->inputWidget) {
            return $this->renderInputWidget($item, [
                'field' => $field,
                'options' => $options,
            ]);
        }

        return $this->renderField($field->model, $field->attribute, $item, $options);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderSearchField($model, $attribute, $item, $options = [])
    {
        return $this->renderField($model, $attribute, $item, $options);
    }

    /**
     * @param \extpoint\yii2\base\Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderForView($model, $attribute, $item, $options = [])
    {
        return Html::encode($model->$attribute);
    }

    /**
     * @param \extpoint\yii2\base\Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderForTable($model, $attribute, $item, $options = [])
    {
        return $this->renderForView($model, $attribute, $item, $options);
    }

    /**
     * @param MetaItem $metaItem
     * @return array
     */
    public function getItems($metaItem)
    {
        return [];
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function getGiiDbType($metaItem)
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @return array
     */
    public function getGiiFieldProps()
    {
        return [];
    }

    /**
     * @param MetaItem $metaItem
     * @param string[] $useClasses
     * @return string|false
     */
    public function getGiiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'string'],
        ];
    }

    /**
     * @param MetaItem $metaItem
     * @return array
     */
    public function getGiiBehaviors($metaItem)
    {
        return [];
    }

    /**
     * @param array $item
     * @param array $config
     * @return object
     */
    protected function renderInputWidget($item, $config = [])
    {
        if (isset($config['field'])) {
            $config['model'] = $config['field']->model;
            $config['attribute'] = $config['field']->attribute;
        }

        if (is_string($this->inputWidget)) {
            $config['class'] = $this->inputWidget;
        } elseif (is_array($this->inputWidget)) {
            $config = array_merge($this->inputWidget, $config);
        }

        if (property_exists($config['class'], 'item')) {
            $config['item'] = $item;
        }

        /** @var \yii\base\Widget $class */
        $class = $config['class'];
        unset($config['class']);

        return $class::widget($config);
    }
}