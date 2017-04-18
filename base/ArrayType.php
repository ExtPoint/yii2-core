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
    public function renderField($field, $item, $options = []) {
        $relationName = ArrayHelper::remove($item, self::OPTION_RELATION_NAME);

        /** @var Model $model */
        $model = $field->model;

        /** @var Model $relationClass */
        $relation = $model->getRelation($relationName);
        $relationClass = $relation->modelClass;

        $models = $relationClass::find()->all();
        $items = ArrayHelper::getColumn(ArrayHelper::index($models, $relationClass::primaryKey()[0]), 'modelLabel');
        $field->dropDownList($items, array_merge($options, [
            'multiple' => $relation->multiple,
        ]));
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = []) {
        $relationName = ArrayHelper::remove($item, self::OPTION_RELATION_NAME);
        if (is_array($model->$relationName)) {
            return implode(', ', array_map(function($model) {
                /** @type Model $model */
                return $model->modelLabel;
            }, $model->$relationName));
        } else if ($model->$relationName instanceof Model) {
            return $model->$relationName->modelLabel;
        }

        return null;
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
    public function getGiiBehaviors($metaItem) {
        return [
            [
                'class' => ManyToManyBehavior::className(),
                'relations' => [
                    [
                        'name' => $metaItem->relationName,
                        'editableAttribute' => $metaItem->name,
                        'autoFill' => false,
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