<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\db\Schema;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;

class DateType extends Type
{
    const OPTION_FORMAT = 'format';

    /**
     * @inheritdoc
     */
    public function renderField($model, $attribute, $item, $options = []) {
        return DatePicker::widget([
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
    public function renderForView($model, $attribute, $item, $options = []) {
        $format = ArrayHelper::remove($item, self::OPTION_FORMAT);
        return \Yii::$app->formatter->asDate($model->$attribute, $format);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return Schema::TYPE_DATE;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return "['date', 'format' => 'php:Y-m-d']";
    }

    /**
     * @inheritdoc
     */
    public function getGiiFieldProps() {
        return [
            self::OPTION_FORMAT => [
                'component' => 'input',
                'label' => 'Format',
                'list' => [
                    'short',
                    'medium',
                    'long',
                    'full'
                ]
            ],
        ];
    }
}