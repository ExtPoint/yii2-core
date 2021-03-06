<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;

class ScheduleType extends Type
{
    const OPTION_SINCE_TIME_ATTRIBUTE = 'sinceTimeAttribute';
    const OPTION_TILL_TIME_ATTRIBUTE = 'tillTimeAttribute';

    /**
     * @return array
     */
    public function frontendConfig()
    {
        return [
            'field' => [
                'component' => 'ScheduleField',
                'refAttributeOptions' => [
                    self::OPTION_SINCE_TIME_ATTRIBUTE,
                    self::OPTION_TILL_TIME_ATTRIBUTE,
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            self::OPTION_SINCE_TIME_ATTRIBUTE => [
                'component' => 'input',
                'label' => 'Since time attribute',
                'list' => 'attributes',
                'style' => [
                    'width' => '80px'
                ]
            ],
            self::OPTION_TILL_TIME_ATTRIBUTE => [
                'component' => 'input',
                'label' => 'Till time attribute',
                'list' => 'attributes',
                'style' => [
                    'width' => '80px'
                ]
            ],
        ];
    }
}