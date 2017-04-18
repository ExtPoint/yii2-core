<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\ArrayType;
use extpoint\yii2\file\widgets\FileInput\FileInput;
use yii\helpers\ArrayHelper;

class FilesType extends ArrayType
{
    /**
     * @inheritdoc
     */
    public function renderField($field, $item, $options = []) {
        $field->parts['{input}'] = FileInput::widget(ArrayHelper::merge(
            [
                'model' => $field->model,
                'attribute' => $field->attribute,
            ],
            $options
        ));
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = []) {

    }

    /**
     * @param \extpoint\yii2\base\Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderForTable($model, $attribute, $item, $options = []) {
        $options = array_merge([
            'width' => 22,
            'height' => 22,
        ], $options);
        return $this->renderForView($model, $attribute, $item, $options);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return false;
    }

}