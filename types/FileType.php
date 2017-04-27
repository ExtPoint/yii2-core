<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use extpoint\yii2\file\FileModule;
use extpoint\yii2\file\models\File;
use extpoint\yii2\file\models\ImageMeta;
use extpoint\yii2\file\widgets\FileInput\FileInput;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class FileType extends Type
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
            $options
        ));
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = [])
    {
        if ($model->$attribute) {
            $file = File::findOne($model->$attribute);
            $url = $file ? $file->previewImageUrl : null;
            if (!$url) {
                return '';
            }

            return Html::img($url, array_merge([
                'width' => 64,
                'height' => 64,
                'alt' => $model->modelLabel,
            ], $options));
        }
        return '';
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
        return Schema::TYPE_INTEGER;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = [])
    {
        return 'integer';
    }
}