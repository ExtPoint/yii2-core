<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use extpoint\yii2\gii\models\MetaItem;
use yii\bootstrap\Html;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class StringType extends Type
{
    const OPTION_TYPE = 'stringType';
    const OPTION_LENGTH = 'stringLength';

    const TYPE_TEXT = 'text';
    const TYPE_EMAIL = 'email';
    const TYPE_PHONE = 'phone';
    const TYPE_PASSWORD = 'password';

    /**
     * @inheritdoc
     */
    public function renderField($model, $attribute, $item, $options = [])
    {
        $type = ArrayHelper::remove($item, self::OPTION_TYPE);
        $options = array_merge(['class' => 'form-control'], $options);

        switch ($type) {
            case self::TYPE_EMAIL:
                return '<div class="input-group"><span class="input-group-addon">@</span>'
                    . Html::activeTextInput($model, $attribute, $options)
                    . '</div>';

            case self::TYPE_PHONE:
                return '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></span>'
                    . Html::activeTextInput($model, $attribute, $options)
                    . '</div>';

            case self::TYPE_PASSWORD:
                return Html::activePasswordInput($model, $attribute, $options);

            case self::TYPE_TEXT:
            default:
                return Html::activeTextInput($model, $attribute, $options);
        }
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = [])
    {
        $type = ArrayHelper::remove($item, self::OPTION_TYPE);
        switch ($type) {
            case self::TYPE_EMAIL:
                \Yii::$app->formatter->asEmail($model->$attribute);
                break;

            case self::TYPE_PASSWORD:
                return '********';
        }

        return parent::renderForView($model, $attribute, $item, $options);
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function getGiiDbType($metaItem)
    {
        return Schema::TYPE_STRING . ($metaItem->stringLength ? '(' . $metaItem->stringLength . ')' : '');
    }

    /**
     * @inheritdoc
     */
    public function getGiiFieldProps()
    {
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
            self::OPTION_LENGTH => [
                'component' => 'input',
                'type' => 'number',
                'label' => 'Length',
                'style' => [
                    'width' => '80px'
                ]
            ],
        ];
    }
}