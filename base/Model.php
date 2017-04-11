<?php

namespace extpoint\yii2\base;

use extpoint\yii2\exceptions\ModelDeleteException;
use extpoint\yii2\exceptions\ModelSaveException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class Model extends ActiveRecord
{
    /**
     * @return array
     */
    public static function meta()
    {
        return [];
    }

    /**
     * @return string
     */
    public static function getRequestParamName()
    {
        return lcfirst(substr(strrchr(static::className(), "\\"), 1)) . ucfirst(static::primaryKey()[0]);
    }

    /**
     * @return string
     */
    public function getModelLabel() {
        foreach (['title', 'label', 'name'] as $attribute) {
            $label = $this->getAttribute($attribute);
            if ($label) {
                return $label;
            }
        }
        return '#' . $this->primaryKey;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [];
        foreach (static::meta() as $attribute => $item) {
            if (isset($item['label']) && is_string($item['label'])) {
                $labels[$attribute] = $item['label'];
            }
        }
        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        $hints = [];
        foreach (static::meta() as $attribute => $item) {
            if (isset($item['hint']) && is_string($item['hint'])) {
                $hints[$attribute] = $item['hint'];
            }
        }
        return $hints;
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @param string|array $condition
     * @return null|static
     * @throws NotFoundHttpException
     */
    public static function findOrPanic($condition)
    {
        $model = static::findOne($condition);
        if (!$model) {
            throw new NotFoundHttpException('Запись не найдена');
        }
        return $model;
    }

    /**
     * @param string[]|null $attributeNames
     * @throws ModelSaveException
     */
    public function saveOrPanic($attributeNames = null)
    {
        if (!$this->save(true, $attributeNames)) {
            throw new ModelSaveException($this);
        }
    }

    /**
     * @throws ModelDeleteException
     */
    public function deleteOrPanic()
    {
        if (!$this->delete()) {
            throw new ModelDeleteException($this);
        }
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canCreate($user)
    {
        return true;
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canUpdate($user)
    {
        return $this->canUpdated();
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canDelete($user)
    {
        return $this->canDeleted();
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canView($user)
    {
        return $this->canUpdate($user);
    }

    /**
     * @return bool
     */
    public function canUpdated()
    {
        return true;
    }

    public function canDeleted()
    {
        return $this->canUpdated() && !$this->isNewRecord;
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert) && $this->canUpdated();
    }

    public function beforeDelete()
    {
        return parent::beforeDelete() && $this->canDeleted();
    }

}
