<?php

namespace extpoint\yii2\types;

use yii\db\Schema;
use yii\helpers\ArrayHelper;

class DateTimeType extends DateType
{
    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'DateTimeField',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderValue($model, $attribute, $item, $options = [])
    {
        $format = ArrayHelper::remove($item, self::OPTION_FORMAT);
        return \Yii::$app->formatter->asDatetime($model->$attribute, $format);
    }

    /**
     * @inheritdoc
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_DATETIME;
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'date', 'format' => $metaItem->format ?: 'php:Y-m-d H:i'],
        ];
    }
}