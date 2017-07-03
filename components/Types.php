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
use extpoint\yii2\widgets\FrontendField;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;

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
 * @property-read array $frontendConfig
 */
class Types extends Component
{
    /**
     * @var Type[]
     */
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
     * @param array|null $field
     * @param array $options
     * @return object|string
     */
    public function renderField($model, $attribute, $field = null, $options = [])
    {
        $item = $this->getMetaItem($model, $attribute);
        if (!$item) {
            return '';
        }

        $type = $this->getTypeByItem($item);
        $config = [
            'class' => FrontendField::className(),
            'model' => $model,
            'attribute' => $attribute,
            'field' => $field,
            'options' => $options,
        ];
        if (is_string($type->inputWidget)) {
            $config['class'] = $type->inputWidget;
        } elseif (is_array($type->inputWidget)) {
            $config = array_merge($config, $type->inputWidget);
        }

        if (property_exists($config['class'], 'metaItem')) {
            $config['metaItem'] = $item;
        }

        /** @var \yii\base\Widget $class */
        $class = $config['class'];
        unset($config['class']);

        $value = $type->renderInputWidget($item, $class, $config);
        if ($value !== null) {
            return $value;
        }

        return $class::widget($config);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderValue($model, $attribute, $options = [])
    {
        $item = $this->getMetaItem($model, $attribute);
        if (!$item) {
            return '';
        }

        $type = $this->getTypeByItem($item);
        $value = $type->renderValue($model, $attribute, $item, $options);
        if ($value !== null) {
            return $value;
        }
        if (is_callable($type->formatter)) {
            return call_user_func($type->formatter, $model->$attribute, $model, $attribute, $item, $options);
        } elseif (is_array($type->formatter) || is_string($type->formatter)) {
            return \Yii::$app->formatter->format($model->$attribute, $type->formatter);
        }

        return Html::encode($model->$attribute);
    }

    /**
     * @return array
     */
    public function getFrontendConfig()
    {
        $config = [];
        foreach ($this->getTypes() as $type) {
            $params = ArrayHelper::merge(
                ($type->frontendConfig() ?: []),
                $type->frontendConfig
            );
            if (!empty($params)) {
                $config[$type->name] = $params;
            }
        }
        return $config;
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
     * @throws Exception
     */
    protected function getTypeByItem($item)
    {
        $appType = !empty($item['appType']) ? $item['appType'] : 'string';
        $component = $this->getType($appType);
        if (!$component) {
            throw new Exception('Not found app type `' . $appType . '`');
        }

        return $component;
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
        $types = [];
        foreach (scandir(__DIR__ . '/../types') as $file) {
            $name = preg_replace('/\.php$/', '', $file);
            $id = lcfirst(preg_replace('/Type$/', '', $name));
            $class = '\extpoint\yii2\types\\' . $name;
            if (class_exists($class)) {
                $types[$id] = [
                    'class' => $class,
                ];
            }
        }
        return $types;
    }
}