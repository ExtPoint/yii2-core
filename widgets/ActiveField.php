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
     * @param array $options
     * @return string
     */
    public function textInput($options = [])
    {
        return static::getTypes()->string->renderFormField($this, $this->getMetaItem([
            'appType' => 'string',
            StringType::OPTION_TYPE => StringType::TYPE_TEXT,
        ]), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function passwordInput($options = [])
    {
        return static::getTypes()->string->renderFormField($this, $this->getMetaItem([
            'appType' => 'string',
            StringType::OPTION_TYPE => StringType::TYPE_PASSWORD,
        ]), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function email($options = [])
    {
        return static::getTypes()->string->renderFormField($this, $this->getMetaItem([
            'appType' => 'string',
            StringType::OPTION_TYPE => StringType::TYPE_EMAIL,
        ]), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function phone($options = [])
    {
        return static::getTypes()->string->renderFormField($this, $this->getMetaItem([
            'appType' => 'string',
            StringType::OPTION_TYPE => StringType::TYPE_PHONE,
        ]), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function file($options = [])
    {
        return static::getTypes()->file->renderFormField($this, $this->getMetaItem(['appType' => 'file']), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function files($options = [])
    {
        return static::getTypes()->files->renderFormField($this, $this->getMetaItem(['appType' => 'files']), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function date($options = [])
    {
        return static::getTypes()->date->renderFormField($this, $this->getMetaItem(['appType' => 'date']), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function dateTime($options = [])
    {
        return static::getTypes()->dateTime->renderFormField($this, $this->getMetaItem(['appType' => 'dateTime']), $options);
    }

    /**
     * @param string $enumClassName
     * @param array $options
     * @return string
     */
    public function enum($enumClassName, $options = [])
    {
        return static::getTypes()->enum->renderFormField($this, $this->getMetaItem([
            'appType' => 'enum',
            EnumType::OPTION_CLASS_NAME => $enumClassName,
        ]), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function wysiwyg($options = [])
    {
        return static::getTypes()->html->renderFormField($this, $this->getMetaItem(['appType' => 'html']), $options);
    }

    /**
     * @param string $currency
     * @param array $options
     * @return string
     */
    public function money($currency, $options = [])
    {
        return static::getTypes()->money->renderFormField($this, $this->getMetaItem([
            'appType' => 'money',
            MoneyType::OPTION_CURRENCY => $currency,
        ]), $options);
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