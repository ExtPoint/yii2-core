<?php

namespace extpoint\yii2\types;

use dosamigos\ckeditor\CKEditor;
use extpoint\yii2\base\Type;
use extpoint\yii2\file\widgets\EditorUploadButton\EditorUploadButton;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class HtmlType extends Type
{
    /**
     * @inheritdoc
     */
    public function renderField($model, $attribute, $item, $options = [])
    {
        $clientOptions = ArrayHelper::remove($options, 'clientOptions', []);
        EditorUploadButton::widget();

        return CKEditor::widget([
            'model' => $model,
            'attribute' => $attribute,
            'options' => $options,
            'clientOptions' => array_merge([
                'toolbar' => [
                    ['name' => 'styles', 'items' => [
                        'Format'
                    ]],
                    ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup'], 'items' => [
                        'Bold',
                        'Italic',
                        'Underline',
                        '-',
                        'RemoveFormat',
                    ]],
                    ['name' => 'paragraph', 'groups' => ['list', 'blocks', 'align'], 'items' => [
                        'NumberedList',
                        'BulletedList',
                        '-',
                        'Blockquote',
                        '-',
                        'JustifyLeft',
                        'JustifyCenter',
                        'JustifyRight',
                    ]],
                    ['name' => 'links', 'items' => [
                        'Link'
                    ]],
                    ['name' => 'insert', 'items' => [
                        'Image'
                    ]],
                ],
                'extraPlugins' => 'fileup',
                'uploadUrl' => Url::to(['/file/upload/editor']),
            ], $clientOptions),
            'preset' => 'custom',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function renderForView($model, $attribute, $item, $options = [])
    {
        return \Yii::$app->formatter->asRaw($model->$attribute);
    }

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem)
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = [])
    {
        // TODO Html validator
        return 'safe';
    }
}