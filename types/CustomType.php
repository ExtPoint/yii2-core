<?php

namespace extpoint\yii2\types;

use extpoint\yii2\base\Type;
use extpoint\yii2\gii\helpers\GiiHelper;
use yii\db\Schema;
use yii\helpers\ArrayHelper;

class CustomType extends Type
{
    const OPTION_DB_TYPE = 'dbType';

    /**
     * @inheritdoc
     */
    public function getGiiDbType($metaItem) {
        return $metaItem->dbType;
    }

    /**
     * @inheritdoc
     */
    public function renderGiiValidator($metaItem, $indent = '', &$useClasses = []) {
        return 'safe';
    }

    /**
     * @inheritdoc
     */
    public function getGiiFieldProps() {
        return [
            self::OPTION_DB_TYPE => [
                'component' => 'input',
                'label' => 'Db Type',
                'list' => GiiHelper::getDbTypes(),
            ],
        ];
    }
}