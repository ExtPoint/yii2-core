<?php

namespace extpoint\yii2\types;

use dosamigos\ckeditor\CKEditor;
use extpoint\yii2\base\Type;
use yii\db\Schema;
use yii\helpers\Url;

class HtmlType extends Type
{
    /**
     * @inheritdoc
     */
    public function renderField($model, $attribute, $item, $options = [])
    {
        return CKEditor::widget([
            'model' => $model,
            'attribute' => $attribute,
            'options' => $options,
            'clientOptions' => [
                'toolbarGroups' => [
                    ['name' => 'styles'],
                    ['name' => 'clipboard', 'groups' => ['clipboard', 'undo']],
                    ['name' => 'document', 'groups' => ['mode']],
                    ['name' => 'links'],
                    ['name' => 'forms'],
                    ['name' => 'tools'],
                    ['name' => 'tools'],
                    '/',
                    ['name' => 'basicstyles', 'groups' => ['basicstyles', 'colors', 'cleanup']],
                    ['name' => 'paragraph', 'groups' => ['list', 'indent', 'blocks', 'align', 'bidi']],
                    ['name' => 'insert'],
                ],
                'removeButtons' => 'Form,Checkbox,Radio,TextField,Textarea,Select,Button,HiddenField',
                'extraPlugins' => 'filebrowser',
                'filebrowserUploadUrl' => Url::to(['/file/upload/editor'])
            ],
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