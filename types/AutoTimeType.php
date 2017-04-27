<?php

namespace extpoint\yii2\types;

use extpoint\yii2\behaviors\TimestampBehavior;

class AutoTimeType extends DateTimeType
{
    const OPTION_TOUCH_ON_UPDATE = 'touchOnUpdate';

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = [])
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getGiiBehaviors($metaItem)
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getGiiFieldProps()
    {
        return [
            self::OPTION_TOUCH_ON_UPDATE => [
                'component' => 'input',
                'label' => 'Is update',
                'type' => 'checkbox',
            ],
        ];
    }
}