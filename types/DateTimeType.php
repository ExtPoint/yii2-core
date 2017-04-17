<?php

namespace extpoint\yii2\types;

use yii\db\Schema;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;

class DateTimeType extends DateType
{
    /**
     * @inheritdoc
     */
    public function renderField($field, $options = []) {
        $field->parts['{input}'] = DateTimePicker::widget([
            'model' => $field->model,
            'attribute' => $field->attribute,
            'options' => $options,
            'pluginOptions' => [
                'format' => 'php:Y-m-d H:i',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $options = []) {
        $format = ArrayHelper::remove($options, self::OPTION_FORMAT);
        return \Yii::$app->formatter->asDatetime($model->$attribute, $format);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return Schema::TYPE_DATETIME;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return "['date', 'format' => 'php:Y-m-d H:i']";
    }
}