<?php

namespace extpoint\yii2\components;

use extpoint\yii2\base\Module;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Class MegaMenu
 * @package extpoint\yii2\components
 * @property array $items
 * @property-read array $activeItem
 */
class MegaMenu extends Component {

    /**
     * @var MegaMenuItem[]
     */
    private $_items = [];
    private $_requestedRoute;
    private $isModulesFetched = false;

    public static function getUrlRulesFromMenu($items) {
            $rules = [];
            foreach ($items as $item) {
                if (isset($item['url']) && is_array($item['url']) && isset($item['urlRule'])) {
                    $defaults = $item['url'];
                    $route = array_shift($defaults);

                    if (is_string($item['urlRule'])) {
                        $rules[] = [
                            'pattern' => $item['urlRule'],
                            'route' => $route,
                            'defaults' => $defaults,
                        ];
                    } elseif (is_array($item['urlRule'])) {
                        if (!isset($item['urlRule']['route'])) {
                            $item['urlRule']['route'] = $route;
                        }
                        if (!isset($item['urlRule']['defaults'])) {
                            $item['urlRule']['defaults'] = $defaults;
                        }
                        $rules[] = $item['urlRule'];
                    }
                }

                if (!empty($item['items'])) {
                    $rules = array_merge($rules, static::getUrlRulesFromMenu($item['items']));
                }
            }
            return $rules;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items) {
        $this->addItems($items);
    }

    /**
     * @return array
     */
    public function getItems() {
        if ($this->isModulesFetched === false) {
            $this->isModulesFetched = true;

            // Fetch items from modules
            foreach (\Yii::$app->getModules() as $id => $module) {
                /** @var Module $module */
                $module = \Yii::$app->getModule($id);
                if ($module instanceof Module) {
                    $this->addItems($module->coreMenus(), false);
                }
            }
        }

        return $this->_items;
    }

    /**
     * @param array $items
     * @param bool|true $append
     */
    public function addItems(array $items, $append = true) {
        $this->_items = $this->mergeItems($this->_items, $items, $append);
    }

    /**
     * @return MegaMenuItem
     * @throws InvalidConfigException
     */
    public function getActiveItem() {
        return $this->getItem($this->getRequestedRoute());
    }

    public function getRequestedRoute() {
        if ($this->_requestedRoute === null) {

            // Set active item
            $parseInfo = \Yii::$app->urlManager->parseRequest(\Yii::$app->request);
            if ($parseInfo) {
                $this->_requestedRoute = [$parseInfo[0] ? '/' . $parseInfo[0] : ''] + $parseInfo[1];
            } else {
                $this->_requestedRoute = ['/' . \Yii::$app->errorHandler->errorAction];
            }
        }
        return $this->_requestedRoute;
    }

    /**
     * @param array $fromItem
     * @param array|int $custom Items or level limit
     * @return array
     * @throws InvalidConfigException
     */
    public function getMenu($fromItem = null, $custom = []) {
        $itemModels = $fromItem ? $this->getItem($fromItem)->items : $this->getItems();

        if (is_int($custom)) {
            // Level limit
            return $this->sliceTreeItems($itemModels, $custom);
        }

        $menu = [];
        if (empty($custom)) {
            // All
            $menu = $itemModels;
        } else {
            // Custom
            // @todo
            /*  foreach ($custom as $item) {
                $menuItemModel = $this->getItem($item);

                // Process items
                if (isset($item['items'])) {
                    $menuItemModel['items'] = $this->getMenu($item['items']);
                } else {
                    unset($menuItemModel['items']);
                }

                // Extend item
                $menuItemModel = array_merge($menuItemModel, $item);

                $menu[] = $menuItemModel;
            }*/
        }

        return array_map(function($itemModel) {
            /** @type MegaMenuItem $itemModel */
            return $itemModel->toArray();
        }, $menu);
    }

    /**
     * Get page title as breadcrumbs labels + site name
     * @param array|null $url Child url or route, default - current route
     * @return string
     */
    public function getTitle($url = null) {
        $titles = array_reverse($this->getBreadcrumbs($url));
        return !empty($titles) ? reset($titles)['label'] : '';
    }

    /**
     * Get page title as breadcrumbs labels + site name
     * @param array|null $url Child url or route, default - current route
     * @param string $separator Separator, default is " - "
     * @return string
     */
    public function getFullTitle($url = null, $separator = ' — ') {
        $title = [];
        foreach (array_reverse($this->getBreadcrumbs($url)) as $item) {
            $title[] = $item['label'];
        }
        $title[] = \Yii::$app->name;
        return implode($separator, $title);
    }

    /**
     * Get items for Breadcrumbs widget
     * @param array|null $url Child url or route, default - current route
     * @return array
     */
    public function getBreadcrumbs($url = null) {
        $url = $url ?: $this->getRequestedRoute();

        // Find child and it parents by url
        $itemModel = $this->getItem($url, $parents);

        if (empty($parents) && $this->isHomeUrl($itemModel->url)) {
            return [];
        }

        $parents = array_reverse((array) $parents);
        $parents[] = [
            'label' => $itemModel->label,
            'url' => $itemModel->url,
        ];

        return $parents;
    }

    /**
     * Find item in menu tree
     * @param string|array $item
     * @param array $parents
     * @return MegaMenuItem
     * @throws InvalidConfigException
     */
    public function getItem($item, &$parents = []) {
        $url = is_array($item) && !$this->isRoute($item) ?
            $item['url'] :
            $item;

        $item = $this->findItemRecursive($url, $this->getItems(), $parents);
        if ($item === null) {
            throw new InvalidConfigException("Not found item " . var_export($url, true) . "");
        }

        return $item;
    }

    public function getItemUrl($item) {
        $item = $this->getItem($item);
        return $item ? $item->url : null;
    }

    /**
     * @param MegaMenuItem[] $items
     * @param int $level
     * @return array
     */
    protected function sliceTreeItems(array $items, $level = 1) {
        if ($level <= 0) {
            return [];
        }

        $menu = [];
        foreach ($items as $itemModel) {
            $item = $itemModel->toArray();

            if (!empty($itemModel->items)) {
                $nextLevel = $level;
                if ($itemModel->url !== null) {
                    $nextLevel--;
                }

                $item['items'] = $this->sliceTreeItems($itemModel->items, $nextLevel);
            }

            if (empty($item['items'])) {
                $item['items'] = null;
            }
            $menu[] = $item;
        }
        return $menu;
    }

    /**
     * @param string|array $url
     * @param MegaMenuItem[] $items
     * @param array $parents
     * @return MegaMenuItem
     */
    protected function findItemRecursive($url, array $items, &$parents) {
        foreach ($items as $itemModel) {
            if ($itemModel->url && $this->isUrlEquals($url, $itemModel->url)) {
                return $itemModel;
            }

            if (!empty($itemModel->items)) {
                $finedItem = $this->findItemRecursive($url, $itemModel->items, $parents);
                if ($finedItem) {
                    $parentItem = $itemModel->toArray();
                    unset($parentItem['items']);
                    $parents[] = $parentItem;

                    return $finedItem;
                }
            }
        }

        return null;
    }

    /**
     * @param string|array|MegaMenuItem $url1
     * @param string|array $url2
     * @return bool
     */
    public function isUrlEquals($url1, $url2) {
        if ($url1 instanceof MegaMenuItem) {
            $url1 = $url1->url;
        }
        if ($url2 instanceof MegaMenuItem) {
            $url2 = $url2->url;
        }

        // Is routes
        if ($this->isRoute($url1) && $this->isRoute($url2)) {
            if (self::normalizeRoute($url1[0]) !== self::normalizeRoute($url2[0])) {
                return false;
            }

            foreach ($url1 as $key => $value) {
                if (is_string($key) && $key !== '#') {
                    if (!isset($url2[$key])) {
                        return false;
                    }

                    if ($value !== null && $url2[$key] !== $value) {
                        return false;
                    }
                }
            }

            return true;
        }

        // Is urls
        if (is_string($url1) && is_string($url2)) {
            return $url1 === $url2;
        }

        return false;
    }

    /**
     * @param string|array $url
     * @return bool
     */
    protected function isHomeUrl($url) {
        if ($this->isRoute($url)) {
            return \Yii::$app->defaultRoute === $url[0];
        }
        return $url === \Yii::$app->homeUrl;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isRoute($value) {
        return is_array($value) && isset($value[0]) && is_string($value[0]);
    }

    /**
     * Function from class \yii\helpers\BaseUrl
     * @param string $route
     * @return string
     */
    protected static function normalizeRoute($route) {
        $route = \Yii::getAlias((string) $route);
        if (strncmp($route, '/', 1) === 0) {
            // absolute route
            return ltrim($route, '/');
        }

        // relative route
        if (\Yii::$app->controller === null) {
            throw new InvalidParamException("Unable to resolve the relative route: $route. No active controller is available.");
        }

        if (strpos($route, '/') === false) {
            // empty or an action ID
            return $route === '' ? \Yii::$app->controller->getRoute() : \Yii::$app->controller->getUniqueId() . '/' . $route;
        } else {
            // relative to module
            return ltrim(\Yii::$app->controller->module->getUniqueId() . '/' . $route, '/');
        }
    }

    protected function mergeItems($baseItems, $items, $append) {
        foreach (array_reverse($items) as $id => $item) {
            // Merge item with group (as key)
            if (is_string($id) && isset($baseItems[$id])) {
                foreach ($item as $key => $value) {
                    if ($key === 'items') {
                        $baseItems[$id]->$key = $this->mergeItems($baseItems[$id]->$key, $value, $append);
                    } elseif (is_array($baseItems[$id]) && is_array($value)) {
                        $baseItems[$id]->$key = $append ?
                            ArrayHelper::merge($baseItems[$id]->$key, $value) :
                            ArrayHelper::merge($value, $baseItems[$id]->$key);
                    } else if ($append || $baseItems[$id]->$key === null) {
                        $baseItems[$id]->$key = $value;
                    }
                }
            } else {

                // Create instance
                if (!($item instanceof MegaMenuItem)) {
                    $item = new MegaMenuItem($item + ['owner' => $this]);
                    $item->items = $this->mergeItems([], $item->items, true);
                }

                // Append or prepend item
                if (is_int($id)) {
                    if ($append) {
                        $baseItems[] = $item;
                    } else {
                        array_unshift($baseItems, $item);
                    }
                } else {
                    if ($append) {
                        $baseItems[$id] = $item;
                    } else {
                        $baseItems = [$id => $item] + $baseItems;
                    }
                }
            }
        }

        return $baseItems;
    }

}