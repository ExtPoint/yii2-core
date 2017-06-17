<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\db\Schema;

class SizeType extends Type
{
    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = [])
    {
        return \Yii::$app->formatter->asShortSize($model->$attribute);
    }

    /**
     * @inheritdoc
     */
    public function getGiiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'number'],
        ];
    }
}