<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\db\Schema;

class IntegerType extends Type
{
    const OPTION_IS_DECIMAL = 'isDecimal';

    /**
     * @inheritdoc
     */
    public function renderField($field, $item, $options = []) {
        $field->textInput($options);
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = []) {
        if (!empty($item[self::OPTION_IS_DECIMAL])) {
            return \Yii::$app->formatter->asDecimal($model->$attribute);
        } else {
            return \Yii::$app->formatter->asInteger($model->$attribute);
        }
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return Schema::TYPE_INTEGER;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return 'integer';
    }

    /**
     * @inheritdoc
     */
    public function getGiiFieldProps() {
        return [
            self::OPTION_IS_DECIMAL => [
                'component' => 'input',
                'label' => 'Is decimal',
                'type' => 'checkbox',
            ],
        ];
    }
}