<?php

namespace extpoint\yii;

abstract class ModelEnum {

    const REQUIRED = false;

    public static function getList() {
        return array();
    }

    public static function getLabel($typeIds) {
        return static::getLabels($typeIds);
    }

    public static function getLabels($names, $separator = ", ") {
        if (!is_array($names)) {
            $names = array($names);
        }

        $fined = array();
        $typeLabels = static::getList();

        foreach ($names as $name) {
            if (isset($typeLabels[$name])) {
                $fined[] = $typeLabels[$name];
            }
        }

        return implode($separator, $fined);
    }

    public static function getMarks() {
        return static::getList();
    }

    public static function getMark($value) {
        if ($value === null) {
            return '';
        }
        $a = static::getMarks();
        return $a[$value];
    }

    public static function getKeys() {
        return array_keys(static::getList());
    }

    public static function toMysqlEnum($default = null) {
        return "enum('" . implode("','", static::getKeys()) . "')" .
            ($default === null ? 'NULL' :
                ($default === self::REQUIRED ? 'NOT NULL' : 'NOT NULL DEFAULT ' . \Yii::$app->db->quoteValue($default))
            );
    }

}
