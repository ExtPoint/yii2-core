<?php

namespace extpoint\yii2\components;

use yii\base\Object;

/**
 * Class MegaMenuItem
 * @package extpoint\yii2\components
 * @property bool $active
 */
class MegaMenuItem extends Object {

    /**
     * @var string
     */
    public $label;

    /**
     * @var string|array
     */
    public $url;

    /**
     * @var string|array
     */
    public $urlRule;

    /**
     * @var string|string[]
     */
    public $roles;

    /**
     * @var bool
     */
    public $visible;

    /**
     * @var bool
     */
    public $encode;

    /**
     * @var MegaMenuItem[]
     */
    public $items = [];

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var array
     */
    public $linkOptions = [];

    /**
     * @var MegaMenu
     */
    public $owner;

    /**
     * @var bool
     */
    public $_active;

    /**
     * @return bool
     */
    public function getActive() {
        if ($this->_active === null) {
            $this->_active = false;

            if ($this->url && $this->owner->isUrlEquals($this->url, $this->owner->getRequestedRoute())) {
                $this->_active = true;
            } else {
                foreach ($this->items as $itemModel) {
                    if ($itemModel->active) {
                        $this->_active = true;
                        break;
                    }
                }
            }
        }
        return $this->_active;
    }

    public function setActive($value) {
        $this->_active = (bool)$value;
    }

    public function getVisible() {
        if ($this->visible !== null) {
            return $this->visible;
        }

        if ($this->roles) {
            foreach ((array)$this->roles as $role) {
                if ($role === '?') {
                    if (\Yii::$app->user->getIsGuest()) {
                        return true;
                    }
                } elseif ($role === '@') {
                    if (!\Yii::$app->user->getIsGuest()) {
                        return true;
                    }
                } elseif (\Yii::$app->user->can($role)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    public function toArray() {
        return [
            'label' => $this->label,
            'url' => $this->url,
            'roles' => $this->roles,
            'visible' => $this->getVisible(),
            'encode' => $this->encode,
            'active' => $this->active,
            'items' => $this->items,
            'options' => $this->options,
            'linkOptions' => $this->linkOptions,
        ];
    }

}