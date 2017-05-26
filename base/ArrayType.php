<?php

namespace extpoint\yii2\base;

use yii\helpers\ArrayHelper;
use arogachev\ManyToMany\behaviors\ManyToManyBehavior;
use yii\helpers\Html;

abstract class ArrayType extends Type
{
    const OPTION_RELATION_NAME = 'relationName';
    const OPTION_LIST_RELATION_NAME = 'listRelationName';

    /**
     * @inheritdoc
     */
    public function renderField($model, $attribute, $item, $options = []) {
        $relationName = ArrayHelper::remove($item, self::OPTION_RELATION_NAME);

        /** @var Model $relationClass */
        $relation = $model->getRelation($relationName);
        $relationClass = $relation->modelClass;

        $models = $relationClass::find()->all();
        $items = ArrayHelper::getColumn(ArrayHelper::index($models, $relationClass::primaryKey()[0]), 'modelLabel');

        // Prepend empty value
        $emptyLabel = ArrayHelper::remove($options, 'emptyLabel');
        if (!empty($item['isRequired']) || $emptyLabel !== null) {
            $items = ArrayHelper::merge(['' => $emptyLabel ?: ''], $items);
        }

        return Html::activeDropDownList($model, $attribute, $items, array_merge($options, [
            'class' => 'form-control',
            'multiple' => $relation->multiple,
        ]));
    }

    /**
     * @inheritdoc
     */
    public function renderSearchField($model, $attribute, $item, $options = []) {
        $options['emptyLabel'] = '';
        return $this->renderField($model, $attribute, $item, $options);
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = []) {
        $relationName = ArrayHelper::remove($item, self::OPTION_RELATION_NAME);
        $models = !is_array($model->$relationName) ? [$model->$relationName] : $model->$relationName;

        return implode(', ', array_map(function($model) use ($options) {
            /** @type Model $model */
            if (!($model instanceof Model)) {
                return '';
            }

            foreach ($model->getModelLinks(\Yii::$app->user->model) as $url) {
                if (\Yii::$app->megaMenu->isAllowAccess($url)) {
                    return Html::a($model->modelLabel, $url, $options);
                }
            }

            return $model->modelLabel;
        }, $models));
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
                'style' => [
                    'width' => '120px',
                ],
            ],
            self::OPTION_LIST_RELATION_NAME => [
                'component' => 'input',
                'label' => 'List relation name',
                'list' => 'relations',
                'style' => [
                    'width' => '120px',
                ],
            ]
        ];
    }

}