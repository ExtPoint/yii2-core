<?php

namespace extpoint\yii2\validators;

use yii\validators\ExistValidator;

class RecordExistValidator extends ExistValidator
{
    /**
     * Consider attribute value successfully validated if it is an instance of the given targetClass and this
     * instance was just created
     *
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (
            $this->targetClass !== null
            && $model->$attribute instanceof $this->targetClass
            && !empty($model->$attribute->isNewRecord)
        ) {
            return;
        }

        parent::validateAttribute($model, $attribute);
    }
}
