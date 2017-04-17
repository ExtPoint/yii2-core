<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\db\Schema;

class BooleanType extends Type
{
    /**
     * @inheritdoc
     */
    public function renderField($field, $options = []) {
        $field->checkbox($options);
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $options = []) {
        return \Yii::$app->formatter->asBoolean($model->$attribute);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return Schema::TYPE_BOOLEAN;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return 'boolean';
    }
}