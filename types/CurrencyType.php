<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use yii\bootstrap\Html;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class CurrencyType extends Type
{
    const OPTION_CURRENCY = 'currency';

    /**
     * @inheritdoc
     */
    public function renderField($model, $attribute, $item, $options = []) {
        $html = Html::activeTextInput($model, $attribute, array_merge(['class' => 'form-control'], $options));

        $currency = ArrayHelper::remove($item, 'currency');
        $icon = in_array($currency, $this->getBootstrapCurrencies())
            ? '<span class="glyphicon glyphicon-' . strtolower($currency) . '"></span>'
            : $currency;
        return '<div class="input-group"><span class="input-group-addon">' . $icon . '</span>' . $html . '</div>';
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = []) {
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