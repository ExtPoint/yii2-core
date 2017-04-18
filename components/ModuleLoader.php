<?php

namespace extpoint\yii2\components;

require_once __DIR__ . '/../base/Module.php';

class ModuleLoader {

    public static $skipFolders = [
        'config',
    ];

    private static $classes;
    private static $addedClasses = [];

    public static function add($name, $className) {
        self::$addedClasses[$name] = $className;
    }

    public static function getBootstrap($appDir = null) {
        $names = [];
        foreach (self::getClasses($appDir) as $name => $moduleClass) {
            if (strpos($name, '.') === false && is_subclass_of($moduleClass, '\yii\base\BootstrapInterface')) {
                $names[] = $name;
            }
        }
        return $names;
    }

    public static function getConfig($appDir = null) {
        $config = [];
        foreach (self::getClasses($appDir) as $name => $moduleClass) {
            if (strpos($name, '.') !== false) {
                list($name, $subName) = explode('.', $name);

                if (!isset($config[$name])) {
                    $config[$name] = [];
                }
                if (!isset($config[$name]['modules'])) {
                    $config[$name]['modules'] = [];
                }
                $config[$name]['modules'][$subName] = [
                    'class' => $moduleClass,
                ];
            } else {
                $config[$name] = [
                    'class' => $moduleClass,
                ];
            }
        }
        return $config;
    }

    public static function getMigrationNamespaces($appDir) {
        $namespaces = [];
        foreach (self::getClasses($appDir) as $name => $moduleClass) {
            $namespace = preg_replace('/[^\\\\]+$/', 'migrations', $moduleClass);

            // Set alias for load migrations
            if (!preg_match('/^\\\\?app\\\\/', $moduleClass)) {
                $moduleDir = dirname((new \ReflectionClass($moduleClass))->getFileName());

                \Yii::setAlias(
                    '@' . str_replace('\\', '/', $namespace),
                    $moduleDir . '/migrations'
                );
            }

            $namespaces[] = $namespace;
        }
        return $namespaces;
    }

    protected static function getClasses($appDir = null) {
        $appDir = $appDir ?: dirname(dirname(__DIR__)) . '/app';

        // Require AppModule class from core
        $path = $appDir . '/core/base/AppModule.php';
        if (file_exists($path)) {
            require_once $path;
        }

        // Require AppAdminModule class from core
        $path = $appDir . '/core/admin/base/AppAdminModule.php';
        if (file_exists($path)) {
            require_once $path;
        }

        if (self::$classes === null) {
            self::$classes = [];

            foreach (scandir($appDir) as $dirName) {
                if (substr($dirName, 0, 1) === '.' || in_array($dirName, self::$skipFolders)) {
                    continue;
                }

                $classPath = $appDir . '/' . $dirName . '/' . ucfirst($dirName) . 'Module.php';
                $className = '\\app\\' . $dirName . '\\' . ucfirst($dirName) . 'Module';

                self::loadClass($classPath, $className);
                self::$classes[$dirName] = $className;

                // Scan submodules
                if (is_dir($appDir . '/' . $dirName)) {
                    foreach (scandir($appDir . '/' . $dirName) as $subDirName) {
                        $subClassPath = $appDir . '/' . $dirName . '/' . $subDirName . '/' . ucfirst($dirName) . ucfirst($subDirName) . 'Module.php';
                        $subClassName = '\\app\\' . $dirName . '\\' . $subDirName . '\\' . ucfirst($dirName) . ucfirst($subDirName) . 'Module';

                        if (file_exists($subClassPath)) {
                            self::loadClass($subClassPath, $subClassName);
                            self::$classes[$dirName . '.' . $subDirName] = $subClassName;
                        }
                    }
                }
            }
        }

        return array_merge(
            self::$addedClasses,
            self::$classes
        );
    }

    protected static function loadClass($path, $name) {
        if (!file_exists($path)) {
            throw new \Exception('Not found module class file: ' . $path);
        }
        require_once $path;

        if (!class_exists($name)) {
            throw new \Exception('Not found module class: ' . $name);
        }
        if (!is_subclass_of($name, '\extpoint\yii2\base\Module')) {
            throw new \Exception('Module class `' . $name . '` is not extends from `\extpoint\yii2\base\Module`');
        }

    }

}