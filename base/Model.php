<?php

namespace extpoint\yii2\base;

use arogachev\ManyToMany\components\ManyToManyRelation;
use extpoint\yii2\components\AuthManager;
use extpoint\yii2\exceptions\ModelDeleteException;
use extpoint\yii2\exceptions\ModelSaveException;
use extpoint\yii2\traits\MetaTrait;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * @property-read string $modelLabel
 */
class Model extends ActiveRecord
{
    use MetaTrait;

    /**
     * @return string
     */
    public static function getRequestParamName()
    {
        try {
            $pk = static::primaryKey()[0];
        } catch (InvalidConfigException $e) {
            $pk = 'id';
        }
        return lcfirst(substr(strrchr(static::className(), "\\"), 1)) . ucfirst($pk);
    }

    /**
     * @return string
     */
    public function getModelLabel()
    {
        foreach (['title', 'label', 'name'] as $attribute) {
            $label = $this->getAttribute($attribute);
            if ($label) {
                return $label;
            }
        }
        return '#' . $this->primaryKey;
    }

    /**
     * @param Model|null $user
     * @return array
     */
    public function getModelLinks($user)
    {
        return [];
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
            throw new ModelDeleteException();
        }
    }

    /**
     * @param string[]|null $names
     */
    public function fillManyMany($names = null)
    {
        if (is_string($names)) {
            $names = [$names];
        }

        if (isset($this->manyToManyRelations)) {
            foreach ($this->manyToManyRelations as $relation) {
                /** @type ManyToManyRelation $relation */

                if ($names === null || in_array($relation->editableAttribute, $names) || in_array($relation->name, $names)) {
                    $relation->fill();
                }
            }
        }
    }

    /**
     * @param array|null $fields
     * @return array
     */
    public function toFrontend($fields = null)
    {
        $fields = $fields ?: ['*'];

        // Detect *
        foreach ($fields as $key => $name) {
            if ($name === '*') {
                unset($fields[$key]);
                $fields = array_merge($fields, $this->fields());
                break;
            }
        }

        $entry = [];
        foreach ($fields as $key => $name) {
            if (is_int($key)) {
                $key = $name;
            }

            if (is_array($name)) {
                // Relations
                $relation = $this->getRelation($key, false);
                if ($relation) {
                    if ($relation->multiple) {
                        $entry[$key] = [];
                        foreach ($this->$key as $childModel) {
                            /** @type Model $childModel */
                            $entry[$key][] = $childModel->toFrontend($name);
                        }
                    } else {
                        $entry[$key] = $this->$key ? $this->$key->toFrontend($name) : null;
                    }
                } else {
                    $child = $this->$key;
                    if (is_array($child)) {
                        $entry[$key] = [];
                        foreach ($child as $childModel) {
                            if ($childModel instanceof Model) {
                                /** @type Model $childModel */
                                $entry[$key][] = $childModel->toFrontend($name);
                            }
                        }
                    } else {
                        $entry[$key] = $child instanceof Model ? $child->toFrontend($name) : null;
                    }
                }
            } else {
                // Attributes
                $value = ArrayHelper::getValue($this, $name);
                $entry[is_string($key) ? $key : $name] = $value;
            }
        }
        return $entry;
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canView($user)
    {
        if (\Yii::$app->has('authManager')) {
            return \Yii::$app->authManager->checkModelAccess($user, static::className(), AuthManager::RULE_MODEL_VIEW);
        }
        return $this->canUpdate($user);
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canCreate($user)
    {
        if (\Yii::$app->has('authManager')) {
            return \Yii::$app->authManager->checkModelAccess($user, static::className(), AuthManager::RULE_MODEL_CREATE);
        }
        return true;
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canUpdate($user)
    {
        if (\Yii::$app->has('authManager')) {
            return \Yii::$app->authManager->checkModelAccess($user, static::className(), AuthManager::RULE_MODEL_UPDATE) && $this->canUpdated();
        }
        return $this->canUpdated();
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function canDelete($user)
    {
        if (\Yii::$app->has('authManager')) {
            return \Yii::$app->authManager->checkModelAccess($user, static::className(), AuthManager::RULE_MODEL_DELETE) && $this->canDeleted();
        }
        return $this->canDeleted();
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
