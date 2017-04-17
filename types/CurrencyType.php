<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class CurrencyType extends Type
{
    const OPTION_CURRENCY = 'currency';

    /**
     * @inheritdoc
     */
    public function renderField($field, $options = []) {
        $field->textInput($options);

        $currency = ArrayHelper::remove($options, 'currency');
        $icon = in_array($currency, $this->getBootstrapCurrencies())
            ? '<span class="glyphicon glyphicon-' . strtolower($currency) . '"></span>'
            : $currency;
        $field->parts['{input}'] = '<div class="input-group"><span class="input-group-addon">' . $icon . '</span>' . $field->parts['{input}'] . '</div>';
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $options = []) {
        return \Yii::$app->formatter->asCurrency($model->$attribute);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return Schema::TYPE_DECIMAL . '(19, 4)';
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return 'number';
    }

    /**
     * @inheritdoc
     */
    public function getGiiFieldProps() {
        return [
            self::OPTION_CURRENCY => [
                'component' => 'input',
                'label' => 'Currency',
                'list' => $this->getBootstrapCurrencies(),
            ],
        ];
    }

    protected function getBootstrapCurrencies() {
        return ['RUB', 'USD', 'EUR', 'BTC', 'XBT', 'YEN', 'JPY', 'GBP'];
    }
}