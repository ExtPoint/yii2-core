<?php

namespace extpoint\yii2\base;

use yii\base\Widget as BaseWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

class Widget extends BaseWidget
{
    public function renderReact($props = [])
    {
        $jsArgs = [
            Json::encode($this->id),
            Json::encode(get_class($this)),
            !empty($props) ? Json::encode($props) : '{}',
        ];
        $this->view->registerJs('__appWidget.render(' . implode(', ', $jsArgs) . ')', View::POS_END, $this->id);
        $this->view->registerJsFile('@static/assets/bundle-' . $this->getBundleName() . '.js', ['position' => View::POS_END]);

        return Html::tag('span', '', ['id' => $this->id]);
    }

    /**
     * Generate bundle name alias extpoint/yii2-frontend npm package
     * @return string
     */
    public function getBundleName() {
        return implode('-', array_filter(array_slice(preg_split('/\\\\/', get_class($this)), 0, -1), function ($name) {
            return preg_match('/[a-z0-9_-]+/', $name) && !in_array($name, ['app', 'widgets']);
        }));
    }
}
