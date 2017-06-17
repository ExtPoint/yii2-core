<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\bootstrap\Html;
use yii\db\Schema;

class TextType extends Type
{
    /**
     * @inheritdoc
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

        return Html::activeTextarea($model, $attribute, array_merge(['class' => 'form-control'], $options));
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = [])
    {
        return \Yii::$app->formatter->asNtext($model->$attribute);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem)
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function getGiiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'string'],
        ];
    }
}