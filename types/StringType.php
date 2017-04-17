<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class StringType extends Type
{
    const OPTION_TYPE = 'stringType';
    const TYPE_TEXT = 'text';
    const TYPE_EMAIL = 'email';
    const TYPE_PHONE = 'phone';
    const TYPE_PASSWORD = 'password';

    /**
     * @inheritdoc
     */
    public function renderField($field, $options = []) {
        $type = ArrayHelper::remove($options, self::OPTION_TYPE);
        switch ($type) {
            case self::TYPE_EMAIL:
                $field->textInput($options);
                $field->parts['{input}'] = '<div class="input-group"><span class="input-group-addon">@</span>' . $field->parts['{input}'] . '</div>';
                break;

            case self::TYPE_PHONE:
                $field->textInput($options);
                $field->parts['{input}'] = '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></span>' . $field->parts['{input}'] . '</div>';
                break;

            case self::TYPE_PASSWORD:
                $field->passwordInput($options);
                break;

            case self::TYPE_TEXT:
            default:
                $field->textInput($options);
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $options = []) {
        $type = ArrayHelper::remove($options, self::OPTION_TYPE);
        switch ($type) {
            case self::TYPE_EMAIL:
                \Yii::$app->formatter->asEmail($model->$attribute);
                break;

            case self::TYPE_PASSWORD:
                return '********';
        }

        return parent::renderForView($model, $attribute, $options);
    }

    /**
     * @inheritdoc
     */
    public function getGiiFieldProps() {
        return [
            self::OPTION_TYPE => [
                'component' => 'select',
                'label' => 'Type',
                'type' => 'checkbox',
                'options' => [
                    self::TYPE_TEXT => 'Text',
                    self::TYPE_EMAIL => 'Email',
                    self::TYPE_PHONE => 'Phone',
                    self::TYPE_PASSWORD => 'Password',
                ],
            ],
        ];
    }
}