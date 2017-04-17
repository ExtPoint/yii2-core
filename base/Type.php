<?php

namespace extpoint\yii2\base;

use extpoint\yii2\gii\models\MetaItem;
use yii\base\Object;
use yii\db\Schema;
use yii\helpers\Html;
use yii\widgets\ActiveField;

abstract class Type extends Object
{
    /**
     * @var string
     */
    public $name;

    /**
     * @param ActiveField $field
     * @param array $options
     */
    public function renderField($field, $options = []) {
        $field->textInput($options);
    }

    /**
     * @param \extpoint\yii2\base\Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderForView($model, $attribute, $options = []) {
        return Html::encode($model->$attribute);
    }

    /**
     * @param \extpoint\yii2\base\Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderForTable($model, $attribute, $options = []) {
        return $this->renderForView($model, $attribute, $options);
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
    public function getGiiBehaviours($metaItem) {
        return [];
    }
}