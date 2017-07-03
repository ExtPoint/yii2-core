<?php

namespace extpoint\yii2\widgets;

use extpoint\yii2\base\Enum;
use extpoint\yii2\base\Model;
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

class FrontendField extends InputWidget
{

    private static $idCounter = 0;

    /**
     * Runs the widget.
     */
    public function run()
    {
        /** @var Model $model */
        $model = $this->model;
        $options = $this->options;
        unset($options['id']);

        // Add model meta
        $jsArgs = [
            Json::encode($model::className()),
            Json::encode($model::meta()),
        ];
        \Yii::$app->view->registerJs('__appTypes.addModelMeta(' . implode(', ', $jsArgs) . ')', View::POS_END, 'app-form-' . $model::className());

        // Add enums
        foreach ($model::meta() as $item) {
            if (!empty($item['enumClassName'])) {
                /** @var Enum $enumClass */
                $enumClass = $item['enumClassName'];

                $jsArgs = [
                    Json::encode($enumClass::className()),
                    Json::encode([
                        'labels' => $enumClass::getLabels(),
                    ]),
                ];
                \Yii::$app->view->registerJs('__appTypes.addEnum(' . implode(', ', $jsArgs) . ')', View::POS_END, 'app-enum-' . $enumClass::className());
            }
        }

        // Render field
        $jsArgs = [
            Json::encode($this->id),
            Json::encode(array_merge([
                'formId' => $this->field ? $this->field->form->id : 'f' . ++self::$idCounter,
                'modelMeta' => $model::className(),
                'prefix' => $model->formName(),
                'attribute' => Html::getAttributeName($this->attribute),
            ], $options)),
        ];
        \Yii::$app->view->registerJs('__appTypes.renderField(' . implode(', ', $jsArgs) . ')', View::POS_END, $this->id);
        return Html::tag('span', '', ['id' => $this->id]);
    }

}