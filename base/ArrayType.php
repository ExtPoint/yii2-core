<?php

namespace extpoint\yii2\base;

use yii\helpers\ArrayHelper;
use arogachev\ManyToMany\behaviors\ManyToManyBehavior;

abstract class ArrayType extends Type
{
    const OPTION_RELATION_NAME = 'relationName';

    /**
     * @inheritdoc
     */
    public function renderField($field, $options = []) {
        $relationName = ArrayHelper::remove($options, self::OPTION_RELATION_NAME);

        /** @var Model $model */
        $model = $field->model;

        /** @var Model $relationClass */
        $relationClass = $model->getRelation($relationName)->modelClass;

        if ($relationClass::find()->count() > 50) {
            $field->textInput($options);
        } else {
            $items = ArrayHelper::getColumn($relationClass::find()->all(), 'modelLabel');
            $field->dropDownList($items, $options);
        }

    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $options = []) {
        $relationName = ArrayHelper::remove($options, self::OPTION_RELATION_NAME);
        return implode(', ', ArrayHelper::getColumn($model->$relationName, 'modelLabel'));
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getGiiBehaviours($metaItem) {
        return [
            [
                'class' => ManyToManyBehavior::className(),
                'relations' => [
                    [
                        'name' => $metaItem->relationName,
                        'editableAttribute' => $metaItem->name,
                    ]
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function getGiiFieldProps() {
        return [
            self::OPTION_RELATION_NAME => [
                'component' => 'input',
                'label' => 'Relation name',
                'list' => 'relations',
            ]
        ];
    }

}