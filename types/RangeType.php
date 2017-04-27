<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class RangeType extends Type
{
    const OPTION_SUB_APP_TYPE = 'subAppType';
    const OPTION_REF_ATTRIBUTE = 'refAttribute';

    const RANGE_POSITION_START = 'start';
    const RANGE_POSITION_END = 'end';

    public $template = '{start} — {end}';

    /**
     * @inheritdoc
     */
    public function renderField($model, $attribute, $item, $options = []) {
        $subAppType = ArrayHelper::remove($item, self::OPTION_SUB_APP_TYPE);
        $refAttribute = ArrayHelper::remove($item, self::OPTION_REF_ATTRIBUTE);
        if ($refAttribute) {
            return Html::tag(
                'div',
                strtr($this->template, [
                    '{start}' => \Yii::$app->types->getType($subAppType)->renderField($model, $attribute, $item, $options),
                    '{end}' => \Yii::$app->types->getType($subAppType)->renderField($model, $refAttribute, $item, $options),
                ]),
                ['class' => 'form-inline']
            );
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = []) {
        $subAppType = ArrayHelper::remove($item, self::OPTION_SUB_APP_TYPE);
        $refAttribute = ArrayHelper::remove($item, self::OPTION_REF_ATTRIBUTE);
        if ($refAttribute) {
            return strtr($this->template, [
                '{start}' => \Yii::$app->types->getType($subAppType)->renderForView($model, $attribute, $item, $options),
                '{end}' => \Yii::$app->types->getType($subAppType)->renderForView($model, $refAttribute, $item, $options),
            ]);
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return \Yii::$app->types->getType($metaItem->subAppType)->getGiiDbType($metaItem);
    }

    /**
     * @inheritdoc
     */
    public function getGiiBehaviors($metaItem) {
        return \Yii::$app->types->getType($metaItem->subAppType)->getGiiBehaviors($metaItem);
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return \Yii::$app->types->getType($metaItem->subAppType)->renderGiiValidator($metaItem, $indent, $useClasses);
    }

    /**
     * @inheritdoc
     */
    public function getGiiFieldProps() {
        return [
            self::OPTION_SUB_APP_TYPE => [
                'component' => 'input',
                'list' => 'types',
                'style' => [
                    'width' => '90px',
                ],
            ],
            self::OPTION_REF_ATTRIBUTE => [
                'component' => 'input',
                'label' => 'To attribute',
                'type' => 'text',
                'list' => 'attributes',
            ],
        ];
    }
}