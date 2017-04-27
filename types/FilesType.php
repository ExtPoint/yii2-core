<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\ArrayType;
use extpoint\yii2\file\models\File;
use extpoint\yii2\file\widgets\FileInput\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class FilesType extends ArrayType
{
    /**
     * @inheritdoc
     */
    public function renderField($model, $attribute, $item, $options = [])
    {
        return FileInput::widget(ArrayHelper::merge(
            [
                'model' => $model,
                'attribute' => $attribute,
            ],
            array_merge($options, [
                'multiple' => true,
            ])
        ));
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = [])
    {
        return implode(' ', array_map(function ($file) use ($model, $options) {
            /** @type File $file */
            $url = $file->previewImageUrl;
            if (!$url) {
                return '';
            }

            return Html::img($url, array_merge([
                'width' => 64,
                'height' => 64,
                'alt' => $model->modelLabel,
            ], $options));
        }, $model->$attribute));
    }

    /**
     * @param \extpoint\yii2\base\Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderForTable($model, $attribute, $item, $options = [])
    {
        $options = array_merge([
            'width' => 22,
            'height' => 22,
        ], $options);
        return $this->renderForView($model, $attribute, $item, $options);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = [])
    {
        return false;
    }

}