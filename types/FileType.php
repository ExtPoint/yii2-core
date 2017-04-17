<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use extpoint\yii2\file\FileModule;
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
    public function renderField($field, $options = []) {
        $field->parts['{input}'] = FileInput::widget(ArrayHelper::merge(
            [
                'model' => $field->model,
                'attribute' => $field->attribute,
            ],
            $options
        ));
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $options = []) {
        if ($model->$attribute) {
            $processor = ArrayHelper::remove($options, 'processor', FileModule::PROCESSOR_NAME_DEFAULT);
            $imageMeta = ImageMeta::findByProcessor($model->$attribute, $processor);
            if ($imageMeta) {
                return Html::img($imageMeta->url, array_merge([
                    'alt' => $model->getModelLabel(),
                    'width' => 100,
                    'height' => 100,
                ], $options));
            }
        }
        return '';
    }

    /**
     * @param \extpoint\yii2\base\Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public function renderForTable($model, $attribute, $options = []) {
        $options = array_merge([
            'width' => 22,
            'height' => 22,
        ], $options);
        return $this->renderForView($model, $attribute, $options);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return Schema::TYPE_INTEGER;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return 'integer';
    }
}