<?php

namespace extpoint\yii2\components;

use extpoint\yii2\Utils;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\View;

class FrontendStateComponent extends Component
{
    /**
     * @var array
     */
    public $state = [];

    public function init()
    {
        parent::init();

        $this->set('config.locale', [
            'language' => \Yii::$app->language,
            'backendTimeZone' => Utils::parseTimeZone(\Yii::$app->timeZone),
        ]);

        if (\Yii::$app->has('types')) {
            $this->set('config.types.config', \Yii::$app->types->frontendConfig);
        }
    }

    /**
     * @param string $path
     * @param mixed $value
     */
    public function set($path, $value)
    {
        $pathNames = explode('.', $path);
        $name = array_pop($pathNames);

        $state = &$this->state;
        foreach ($pathNames as $key) {
            if (!isset($state[$key])) {
                $state[$key] = [];
            }
            $state = &$state[$key];
        }

        if (isset($state[$name]) && is_array($state[$name]) && is_array($value)) {
            $state[$name] = array_merge($state[$name], $value);
        } else {
            $state[$name] = $value;
        }
    }

    /**
     * @param string $path
     * @param mixed $value
     */
    public function add($path, $value)
    {
        $this->set($path, [$value]);
    }

    /**
     * @param View $view
     */
    public function register($view)
    {
        $view->registerJs('window.APP_REDUX_PRELOAD_STATES = [];', View::POS_HEAD);
        $view->registerJs('window.APP_REDUX_PRELOAD_STATES.push(' . Json::encode($this->state) . ')', View::POS_HEAD);
    }

}