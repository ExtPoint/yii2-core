<?php

namespace extpoint\yii;

class ModelSaveException extends Exception {

    public $errors = [];

    /**
     * @param \yii\base\Model $model
     */
    public function __construct($model) {

        $this->errors = $model->errors;

        $text = print_r($this->errors, true);

        parent::__construct('Cannot save model ' . $model->className() . ', errors: ' . $text);
    }

}
