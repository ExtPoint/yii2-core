<?php

namespace extpoint\yii2\base;

use extpoint\yii2\gii\models\MetaItem;
use extpoint\yii2\widgets\ActiveField;
use yii\base\Object;
use yii\db\Schema;
use yii\helpers\Html;

abstract class Type extends Object
{
    /**
     * @var string
     */
    public $name;

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderField($model, $attribute, $item, $options = []) {
        return Html::activeTextInput($model, $attribute, array_merge(['class' => 'form-control'], $options));
    }

    /**
     * @param ActiveField $field
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderFormField($field, $item, $options = []) {
        return $this->renderField($field->model, $field->attribute, $item, $options);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderSearchField($model, $attribute, $item, $options = []) {
        return $this->renderField($model, $attribute, $item, $options);
    }

    /**
     * @param \extpoint\yii2\base\Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderForView($model, $attribute, $item, $options = []) {
        return Html::encode($model->$attribute);
    }

    /**
     * @param \extpoint\yii2\base\Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string
     */
    public function renderForTable($model, $attribute, $item, $options = []) {
        return $this->renderForView($model, $attribute, $item, $options);
    }

    /**
     * @param MetaItem $metaItem
     * @return array
     */
    public function getItems($metaItem) {
        return [];
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function getGiiDbType($metaItem) {
        return Schema::TYPE_STRING;
    }

    /**
     * @return array
     */
    public function getGiiFieldProps() {
        return [];
    }

    /**
     * @param MetaItem $metaItem
     * @param string $indent
     * @param string[] $useClasses
     * @return string|false
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return "['string']";
    }

    /**
     * @param MetaItem $metaItem
     * @return array
     */
    public function getGiiBehaviors($metaItem) {
        return [];
    }
}