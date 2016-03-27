<?php

namespace extpoint\yii2;

use yii\db\ActiveQuery;

class ActiveRecord extends \yii\db\ActiveRecord {

    /**
     * @param string $class
     * @param string $secondaryToThat
     * @param string $viaTable
     * @param string $secondaryToThis
     * @param callable|null $customization
     * @return ActiveQuery
     */
    public function manyMany($class, $secondaryToThat, $viaTable, $secondaryToThis, $customization = null) {
        return $this->hasMany($class, ['id' => $secondaryToThat])
            ->viaTable($viaTable, [$secondaryToThis => 'id'], $customization);
    }

    /**
     * @param string $key
     * @param string $value
     * @param null|array $condition
     * @param null|string $orderBy
     * @return string[]
     */
    public static function findAsHash($key, $value, $condition = null, $orderBy = null) {

        $result = [];
        foreach (static::find()->where($condition)->asArray()->orderBy($orderBy)->all() as $row) {
            $result[$row[$key]] = $row[$value];
        }

        return $result;
    }

    /**
     * @param string[]|null $attributeNames
     * @throws ModelSaveException
     */
    public function saveOrPanic($attributeNames = null) {

        if (!$this->save(true, $attributeNames)) {
            throw new ModelSaveException($this);
        }
    }

}
