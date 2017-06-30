<?php

namespace extpoint\yii2\base;

use extpoint\yii2\gii\models\MetaItem;
use yii\base\Object;
use yii\db\Schema;
use yii\base\Widget;

abstract class Type extends Object
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string|array
     */
    public $inputWidget;

    /**
     * @var string|array|callable
     */
    public $formatter;

    /**
     * @var array
     */
    public $frontendConfig = [];

    /**
     * @param array $item
     * @param Widget $class
     * @param array $config
     * @return string
     */
    public function renderInputWidget($item, $class, $config)
    {
        return null;
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $item
     * @param array $options
     * @return string|null
     */
    public function renderValue($model, $attribute, $item, $options)
    {
        return null;
    }

    /**
     * @return array|null
     */
    public function frontendConfig()
    {
        return null;
    }

    /**
     * @param MetaItem $metaItem
     * @return array
     */
    public function getGiiJsMetaItem($metaItem, &$import = [])
    {
        /** @var Model $metaClass */
        $metaClass = $metaItem->metaClass->modelClass->className;
        $meta = $metaClass::meta();

        return isset($meta[$metaItem->name]) ? $meta[$metaItem->name] : [];
    }

    /**
     * @param MetaItem $metaItem
     * @return array
     */
    public function getItems($metaItem)
    {
        return [];
    }

    /**
     * @param MetaItem $metaItem
     * @param string[] $useClasses
     * @return array
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        return [
            [$metaItem->name, 'string'],
        ];
    }

    /**
     * @param MetaItem $metaItem
     * @return array
     */
    public function giiBehaviors($metaItem)
    {
        return [];
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @return array
     */
    public function giiOptions()
    {
        return [];
    }
}