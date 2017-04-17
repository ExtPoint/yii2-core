<?php

namespace extpoint\yii2\components;

use extpoint\yii2\base\Type;
use yii\base\Component;

class Types extends Component
{
    public $types = [];
    
    public function init() {
        parent::init();
        
        $this->types = array_merge($this->getDefaultTypes(), $this->types);
    }

    /**
     * @param string $name
     * @return Type|null
     */
    public function getType($name) {
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
     * @return Type[]
     */
    public function getTypes() {
        return array_map(function($name) {
            return $this->getType($name);
        }, array_keys($this->types));
    }
    
    protected function getDefaultTypes() {
        return [
            'autoTime' => '\extpoint\yii2\types\AutoTimeType',
            'boolean' => '\extpoint\yii2\types\BooleanType',
            'currency' => '\extpoint\yii2\types\CurrencyType',
            'custom' => '\extpoint\yii2\types\CustomType',
            'dateTime' => '\extpoint\yii2\types\DateTimeType',
            'date' => '\extpoint\yii2\types\DateType',
            'enum' => '\extpoint\yii2\types\EnumType',
            'files' => '\extpoint\yii2\types\FilesType',
            'file' => '\extpoint\yii2\types\FileType',
            'html' => '\extpoint\yii2\types\HtmlType',
            'integer' => '\extpoint\yii2\types\IntegerType',
            'primaryKey' => '\extpoint\yii2\types\PrimaryKeyType',
            'relation' => '\extpoint\yii2\types\RelationType',
            'size' => '\extpoint\yii2\types\SizeType',
            'string' => '\extpoint\yii2\types\StringType',
            'text' => '\extpoint\yii2\types\TextType',
        ];
    }
}