<?php

namespace extpoint\yii2\components;

require_once __DIR__ . '/../base/AppModule.php';

class ModuleLoader {

    public static $skipFolders = [
        'config',
    ];

    private static $classes;

    public static function getBootstrap($appDir = null) {
        $names = [];
        foreach (self::getClasses($appDir) as $name => $moduleClass) {
            if (is_subclass_of($moduleClass, '\yii\base\BootstrapInterface')) {
                $names[] = $name;
            }
        }
        return $names;
    }

    public static function getConfig($appDir = null) {
        $config = [];
        foreach (self::getClasses($appDir) as $name => $moduleClass) {
            $config[$name] = [
                'class' => $moduleClass,
            ];
        }
        return $config;
    }

    protected static function getClasses($appDir = null) {
        // @todo submodules support or not need?

        $appDir = $appDir ?: dirname(dirname(__DIR__)) . '/app';

        if (self::$classes === null) {
            self::$classes = [];

            foreach (scandir($appDir) as $dirName) {
                if (substr($dirName, 0, 1) === '.' || in_array($dirName, self::$skipFolders)) {
                    continue;
                }

                $classPath = $appDir . '/' . $dirName . '/' . ucfirst($dirName) . 'Module.php';
                if (!file_exists($classPath)) {
                    throw new \Exception('Not found module class file: ' . $classPath);
                }
                require_once $classPath;

                $className = '\app\\' . $dirName . '\\' . ucfirst($dirName) . 'Module';
                if (!class_exists($className)) {
                    throw new \Exception('Not found module class: ' . $className);
                }
                if (!is_subclass_of($className, '\extpoint\yii2\base\AppModule')) {
                    throw new \Exception('Module class `' . $className . '` is not extends from `\extpoint\yii2\base\AppModule`');
                }

                self::$classes[$dirName] = $className;
            }
        }
        return self::$classes;
    }

}