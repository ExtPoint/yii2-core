<?php

namespace extpoint\yii2\components;

use extpoint\megamenu\MegaMenuItem;
use extpoint\yii2\base\Model;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\rbac\Assignment;
use yii\rbac\PhpManager;

class AuthManager extends PhpManager
{
    const ROLE_GUEST = 'guest';
    const RULE_SEPARATOR = '::';
    const RULE_PREFIX_MODEL = 'm';
    const RULE_PREFIX_ACTION = 'a';
    const ACTION_SELF = 'self';
    const RULE_MODEL_VIEW = 'view';
    const RULE_MODEL_CREATE = 'create';
    const RULE_MODEL_UPDATE = 'update';
    const RULE_MODEL_DELETE = 'delete';

    public $itemFile = '@app/config/rbac/items.php';
    public $assignmentFile = '@app/config/rbac/assignments.php';
    public $ruleFile = '@app/config/rbac/rules.php';

    /**
     * @param Model|null $user
     * @param Model|string $model
     * @param string $rule
     * @return bool
     */
    public function checkModelAccess($user, $model, $rule)
    {
        if ($model instanceof Object) {
            $model = $model::className();
        }

        $permissionName = implode(self::RULE_SEPARATOR, [
            self::RULE_PREFIX_MODEL,
            $model,
            $rule,
        ]);
        $userId = $user ? $user->primaryKey : null;

        return $this->checkAccess($userId, $permissionName);
    }

    /**
     * @param Model|null $user
     * @param Model|string $model
     * @param string $attribute
     * @param string $rule
     * @return bool
     */
    public function checkAttributeAccess($user, $model, $attribute, $rule)
    {
        if ($model instanceof Object) {
            $model = $model::className();
        }

        $permissionName = implode(self::RULE_SEPARATOR, [
            self::RULE_PREFIX_MODEL,
            $model,
            $attribute,
            $rule,
        ]);
        $userId = $user ? $user->primaryKey : null;

        return $this->checkAccess($userId, $permissionName);
    }

    /**
     * @param $user
     * @param MegaMenuItem $menuItem
     * @return bool
     */
    public function checkMenuAccess($user, $menuItem)
    {
        $permissionName = implode(self::RULE_SEPARATOR, array_merge(
            [self::RULE_PREFIX_ACTION],
            $menuItem->pathIds,
            count($menuItem->items) > 0 && !$menuItem->redirectToChild ? [self::ACTION_SELF] : []
        ));
        $userId = $user ? $user->primaryKey : null;

        return $this->checkAccess($userId, $permissionName);
    }

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        if ($userId === null) {
            return [
                self::ROLE_GUEST => new Assignment([
                    'roleName' => self::ROLE_GUEST,
                ]),
            ];
        }

        /** @var Model $userClass */
        $userClass = \Yii::$app->user->identityClass;
        $user = $userClass::findOne($userId);
        if (!$user) {
            return [];
        }

        return [
            $user->role => new Assignment([
                'userId' => $userId,
                'roleName' => $user->role,
            ]),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAssignment($roleName, $userId)
    {
        $assignments = $this->getAssignments($userId);
        return ArrayHelper::getValue($assignments, '0.roleName') === $roleName ? $assignments[0] : null;
    }

    /**
     * @inheritdoc
     */
    public function getUserIdsByRole($roleName)
    {
        /** @var Model $userClass */
        $userClass = \Yii::$app->user->identityClass;
        return $userClass::find()
            ->select('id')
            ->where([
                'role' => $roleName,
            ])
            ->column();
    }
}