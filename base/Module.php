<?php

namespace extpoint\yii2\base;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * @package extpoint\yii2\base
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    public $layout = '@app/core/layouts/web';

    /**
     * @return static
     * @throws Exception
     */
    public static function getInstance()
    {
        if (!preg_match('/([^\\\]+)Module$/', static::className(), $match)) {
            throw new Exception('Cannot auto get module id by class name: ' . static::className());
        }

        $id = lcfirst($match[1]);
        return \Yii::$app->getModule($id);
    }

    /**
     * @param Application $app
     */
    public function bootstrap($app)
    {
        // Bootstrap submodules
        foreach ($this->modules as $module) {
            if (is_array($module)) {
                $moduleName = explode('\\', trim($module['class'], '\\'))[2];
                $module = $this->getModule($moduleName);
            }
            if ($module instanceof BootstrapInterface) {
                $module->bootstrap($app);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        // Layout for admin modules (as submodule in application modules)
        if ($this->id === 'admin') {
            $this->layout = '@app/core/admin/layouts/web';
        }

        parent::init();

        $this->initCoreComponents();
    }

    protected function initCoreComponents()
    {
        // Create core components
        $coreComponents = $this->coreComponents();
        foreach ($coreComponents as $id => $config) {
            if (is_string($this->$id)) {
                $config = ['class' => $this->$id];
            } elseif (is_array($this->$id)) {
                $config = ArrayHelper::merge($config, $this->$id);
            }
            $this->$id = \Yii::createObject($config);
        }
    }

    protected function coreComponents()
    {
        return [];
    }

    public function coreUrlRules()
    {
        return [];
    }

    public function coreMenu()
    {
        return [];
    }

}