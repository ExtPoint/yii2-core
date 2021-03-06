<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use extpoint\yii2\gii\models\MetaItem;
use extpoint\yii2\gii\models\ValueExpression;
use extpoint\yii2\validators\WordsValidator;
use yii\db\Schema;
use yii\helpers\StringHelper;

class StringType extends Type
{
    const OPTION_TYPE = 'stringType';
    const OPTION_LENGTH = 'stringLength';

    const TYPE_TEXT = 'text';
    const TYPE_WORDS = 'words';

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'StringField',
            ]
        ];
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_STRING . ($metaItem->stringLength ? '(' . $metaItem->stringLength . ')' : '');
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        $validators = [
            [$metaItem->name, 'string', 'max' => $metaItem->stringLength ?: 255],
        ];

        switch ($metaItem->stringType) {
            case self::TYPE_WORDS:
                $wordsValidatorClass = WordsValidator::className();
                $useClasses[] = $wordsValidatorClass;
                $validators[] = [$metaItem->name, new ValueExpression(StringHelper::basename($wordsValidatorClass) . '::className()')];
                break;
        }

        return $validators;
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            self::OPTION_TYPE => [
                'component' => 'select',
                'label' => 'Type',
                'options' => [
                    self::TYPE_TEXT => 'Text',
                    self::TYPE_WORDS => 'Words',
                ],
            ],
            self::OPTION_LENGTH => [
                'component' => 'input',
                'type' => 'number',
                'label' => 'Length',
                'style' => [
                    'width' => '80px'
                ]
            ],
        ];
    }
}