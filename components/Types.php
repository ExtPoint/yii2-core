<?php

namespace extpoint\yii2\components;

use extpoint\yii2\base\Model;
use extpoint\yii2\base\Type;
use extpoint\yii2\types\AutoTimeType;
use extpoint\yii2\types\BooleanType;
use extpoint\yii2\types\DoubleType;
use extpoint\yii2\types\MoneyType;
use extpoint\yii2\types\CustomType;
use extpoint\yii2\types\DateTimeType;
use extpoint\yii2\types\DateType;
use extpoint\yii2\types\EnumType;
use extpoint\yii2\types\FilesType;
use extpoint\yii2\types\FileType;
use extpoint\yii2\types\HtmlType;
use extpoint\yii2\types\IntegerType;
use extpoint\yii2\types\PrimaryKeyType;
use extpoint\yii2\types\RangeType;
use extpoint\yii2\types\RelationType;
use extpoint\yii2\types\SizeType;
use extpoint\yii2\types\StringType;
use extpoint\yii2\types\TextType;
use extpoint\yii2\widgets\ActiveField;
use yii\base\Component;
use yii\helpers\Html;

/**
 * @property-read AutoTimeType $autoTime
 * @property-read BooleanType $boolean
 * @property-read MoneyType $money
 * @property-read CustomType $custom
 * @property-read DateTimeType $dateTime
 * @property-read DateType $date
 * @property-read DoubleType $double
 * @property-read EnumType $enum
 * @property-read FilesType $files
 * @property-read FileType $file
 * @property-read HtmlType $html
 * @property-read IntegerType $integer
 * @property-read PrimaryKeyType $primaryKey
 * @property-read RangeType $range
 * @property-read RelationType $relation
 * @property-read SizeType $size
 * @property-read StringType $string
 * @property-read TextType $text
 */
class Types extends Component
{
    public $types = [];

    public function init()
    {
        parent::init();
        $this->types = array_merge($this->getDefaultTypes(), $this->types);
    }

    public function __get($name)
    {
        if (isset($this->types[$name])) {
            return $this->getType($name);
        }

        return parent::__get($name);
    }

    /**
     * @param string $name
     * @return Type|null
     */
    public function getType($name)
    {
        if (!isset($this->types[$name])) {
            return null;
        }

        if (is_array($this->types[$name]) || is_string($this->types[$name])) {
            $this->types[$name] = \Yii::createObject($this->types[$name]);
            $this->types[$name]->name = $name;
        }
        return $this->types[$name];
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderField($model, $attribute, $options = [])
    {
        $item = $this->getMetaItem($model, $attribute);
        return $item ? $this->getTypeByItem($item)->renderField($model, $attribute, $item, $options) : '';
    }

    /**
     * @param ActiveField $field
     * @param array $options
     * @return string
     */
    public function renderFormField($field, $options = [])
    {
        $item = $this->getMetaItem($field->model, $field->attribute);
        if ($item) {
            $this->getTypeByItem($item)->renderFormField($field, $item, $options);
        }
        return $field;
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderSearchField($model, $attribute, $options = [])
    {
        $item = $this->getMetaItem($model, $attribute);
        return $item ? $this->getTypeByItem($item)->renderSearchField($model, $attribute, $item, $options) : '';
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderForTable($model, $attribute, $options = [])
    {
        $item = $this->getMetaItem($model, $attribute);
        return $item ? $this->getTypeByItem($item)->renderForTable($model, $attribute, $item, $options) : '';
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderForView($model, $attribute, $options = [])
    {
        $item = $this->getMetaItem($model, $attribute);
        return $item ? $this->getTypeByItem($item)->renderForView($model, $attribute, $item, $options) : '';
    }

    /**
     * @return Type[]
     */
    public function getTypes()
    {
        return array_map(function ($name) {
            return $this->getType($name);
        }, array_keys($this->types));
    }

    /**
     * @param array $item
     * @return Type|null
     */
    protected function getTypeByItem($item)
    {
        $appType = !empty($item['appType']) ? $item['appType'] : 'string';
        return $this->getType($appType);
    }

    /**
     * @param Model $modelClass
     * @param string $attribute
     * @return array|null
     */
    protected function getMetaItem($modelClass, $attribute)
    {
        if (is_object($modelClass)) {
            $modelClass = get_class($modelClass);
        }

        $meta = $modelClass::meta();
        $attribute = Html::getAttributeName($attribute);

        return isset($meta[$attribute]) ? $meta[$attribute] : null;
    }

    protected function getDefaultTypes()
    {
        return [
            'autoTime' => '\extpoint\yii2\types\AutoTimeType',
            'boolean' => '\extpoint\yii2\types\BooleanType',
            'money' => '\extpoint\yii2\types\MoneyType',
            'custom' => '\extpoint\yii2\types\CustomType',
            'dateTime' => '\extpoint\yii2\types\DateTimeType',
            'date' => '\extpoint\yii2\types\DateType',
            'double' => '\extpoint\yii2\types\DoubleType',
            'enum' => '\extpoint\yii2\types\EnumType',
            'files' => '\extpoint\yii2\types\FilesType',
            'file' => '\extpoint\yii2\types\FileType',
            'html' => '\extpoint\yii2\types\HtmlType',
            'integer' => '\extpoint\yii2\types\IntegerType',
            'primaryKey' => '\extpoint\yii2\types\PrimaryKeyType',
            'range' => '\extpoint\yii2\types\RangeType',
            'relation' => '\extpoint\yii2\types\RelationType',
            'size' => '\extpoint\yii2\types\SizeType',
            'string' => '\extpoint\yii2\types\StringType',
            'text' => '\extpoint\yii2\types\TextType',
        ];
    }
}