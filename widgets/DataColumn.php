<?php

namespace extpoint\yii2\widgets;

use extpoint\yii2\base\Model;

class DataColumn extends \yii\grid\DataColumn
{
    protected function renderFilterCellContent()
    {
        $model = $this->grid->filterModel;
        if ($this->filter === null && $this->attribute && $model instanceof Model) {
            $meta = $model::meta();
            if (isset($meta[$this->attribute])) {
                if (empty($meta[$this->attribute]['showInFilter'])) {
                    return $this->grid->emptyCell;
                }
                return \Yii::$app->types->renderSearchField($model, $this->attribute);
            }
        }

        return parent::renderFilterCellContent();
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null && $this->value === null && $this->format === 'text' && $this->attribute && $model instanceof Model) {
            return \Yii::$app->types->renderForTable($model, $this->attribute, $this->options);
        }

        return parent::renderDataCellContent($model, $this->attribute, $index);
    }
}