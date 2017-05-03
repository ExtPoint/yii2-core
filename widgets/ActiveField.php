<?php

namespace extpoint\yii2\widgets;

use extpoint\yii2\base\Model;
use extpoint\yii2\types\EnumType;
use extpoint\yii2\types\MoneyType;
use extpoint\yii2\types\StringType;
use yii\helpers\Html;

class ActiveField extends \yii\bootstrap\ActiveField
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @return \extpoint\yii2\components\Types
     */
    public static function getTypes()
    {
        return \Yii::$app->types;
    }

    /**
     * @inheritdoc
     */
    public function render($content = null)
    {
        if ($content === null && !isset($this->parts['{input}']) && $this->model instanceof Model) {
            $html = \Yii::$app->types->renderFormField($this, $this->inputOptions);
            if ($html) {
                $this->parts['{input}'] = $html;
            }
        }
        return parent::render($content);
    }

    /**
     * @param array $options
     * @return static
     */
    public function textInput($options = [])
    {
        static::getTypes()->string->renderFormField($this, $this->getMetaItem([
            'appType' => 'string',
            StringType::OPTION_TYPE => StringType::TYPE_TEXT,
        ]), $options);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function passwordInput($options = [])
    {
        static::getTypes()->string->renderFormField($this, $this->getMetaItem([
            'appType' => 'string',
            StringType::OPTION_TYPE => StringType::TYPE_PASSWORD,
        ]), $options);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function email($options = [])
    {
        static::getTypes()->string->renderFormField($this, $this->getMetaItem([
            'appType' => 'string',
            StringType::OPTION_TYPE => StringType::TYPE_EMAIL,
        ]), $options);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function phone($options = [])
    {
        static::getTypes()->string->renderFormField($this, $this->getMetaItem([
            'appType' => 'string',
            StringType::OPTION_TYPE => StringType::TYPE_PHONE,
        ]), $options);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function file($options = [])
    {
        static::getTypes()->file->renderFormField($this, $this->getMetaItem(['appType' => 'file']), $options);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function files($options = [])
    {
        static::getTypes()->files->renderFormField($this, $this->getMetaItem(['appType' => 'files']), $options);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function date($options = [])
    {
        static::getTypes()->date->renderFormField($this, $this->getMetaItem(['appType' => 'date']), $options);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function dateTime($options = [])
    {
        static::getTypes()->dateTime->renderFormField($this, $this->getMetaItem(['appType' => 'dateTime']), $options);
        return $this;
    }

    /**
     * @param string $enumClassName
     * @param array $options
     * @return static
     */
    public function enum($enumClassName, $options = [])
    {
        static::getTypes()->enum->renderFormField($this, $this->getMetaItem([
            'appType' => 'enum',
            EnumType::OPTION_CLASS_NAME => $enumClassName,
        ]), $options);
        return $this;
    }

    /**
     * @param array $options
     * @return static
     */
    public function wysiwyg($options = [])
    {
        static::getTypes()->html->renderFormField($this, $this->getMetaItem(['appType' => 'html']), $options);
        return $this;
    }

    /**
     * @param string $currency
     * @param array $options
     * @return static
     */
    public function money($currency, $options = [])
    {
        static::getTypes()->money->renderFormField($this, $this->getMetaItem([
            'appType' => 'money',
            MoneyType::OPTION_CURRENCY => $currency,
        ]), $options);
        return $this;
    }

    /**
     * @param array $custom
     * @return array
     */
    protected function getMetaItem($custom = []) {
        $item = [];
        if ($this->model instanceof Model) {
            /** @var Model $modelClass */
            $modelClass = get_class($this->model);

            $meta = $modelClass::meta();
            $attribute = Html::getAttributeName($this->attribute);
            if (isset($meta[$attribute])) {
                $item = $meta[$attribute];
            }
        }

        return array_merge($item, $custom);
    }


}