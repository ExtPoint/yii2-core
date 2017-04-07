<?php

namespace extpoint\yii2\base;

use yii\base\Widget as BaseWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

class Widget extends BaseWidget
{
    public function renderReact($props)
    {
        $jsArgs = [
            Json::encode($this->id),
            Json::encode(get_class($this)),
            !empty($props) ? Json::encode($props) : '{}',
        ];
        $this->view->registerJs('__appWidget.render(' . implode(', ', $jsArgs) . ')', View::POS_END, $this->id);

        $widgetName = substr(strrchr(__CLASS__, "\\"), 1);
        $this->view->registerJsFile("@static/assets/bundle-$widgetName.js", ['position' => View::POS_END]);

        return Html::tag('span', '', ['id' => $this->id]);
    }
}
