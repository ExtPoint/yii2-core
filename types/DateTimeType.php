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
    public function renderField($model, $attribute, $item, $options = [])
    {
        return DateTimePicker::widget([
            'model' => $model,
            'attribute' => $attribute,
            'options' => $options,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = [])
    {
        $format = ArrayHelper::remove($item, self::OPTION_FORMAT);
        return \Yii::$app->formatter->asDatetime($model->$attribute, $format);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem)
    {
        return Schema::TYPE_DATETIME;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = [])
    {
        return "['date', 'format' => 'php:Y-m-d H:i']";
    }
}