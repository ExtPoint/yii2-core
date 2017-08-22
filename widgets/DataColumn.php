<?php

namespace extpoint\yii2\widgets;

use extpoint\yii2\base\FormModel;
use extpoint\yii2\base\Model;
use yii\helpers\ArrayHelper;

class DataColumn extends \yii\grid\DataColumn
{
    /**
     * @var array
     */
    public $controllerMeta;

    protected function renderFilterCellContent()
    {
        $model = $this->grid->filterModel;
        if ($this->filter === null && $this->attribute && ($model instanceof Model || $model instanceof FormModel)) {
            if (!ArrayHelper::getValue($this->controllerMeta, 'formModelAttributes.' . $this->attribute . '.showInFilter')) {
                return $this->grid->emptyCell;
            }
            return \Yii::$app->types->renderField($model, $this->attribute, null, ['layout' => 'inline']);
        }

        return parent::renderFilterCellContent();
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null && $this->value === null && $this->format === 'text' && $this->attribute && $model instanceof Model) {
            $options = $this->options;
            $options['forTable'] = true;
            return \Yii::$app->types->renderValue($model, $this->attribute, $options);
        }

        return parent::renderDataCellContent($model, $this->attribute, $index);
    }
}