<?php

namespace app\views;

use app\core\widgets\CrudControls;
use extpoint\yii2\base\CrudController;
use extpoint\yii2\base\Model;
use extpoint\yii2\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\View;

/* @var $this View */
/* @var $model Model */

/** @var CrudController $controller */
$controller = $this->context;

/** @var array $meta */
$meta = $controller::meta();

?>
<h1><?= \Yii::$app->megaMenu->getTitle() ?></h1>

<?= CrudControls::widget([
    'model' => $model,
    'actionParams' => $controller::getMetaUrlParams(),
]) ?>

<?php $form = ActiveForm::begin() ?>

<?= $form->fields($model, array_keys(array_filter(ArrayHelper::getValue($meta, 'modelAttributes'), function($item) {
    return !empty($item['showInForm']);
}))) ?>
<?= $form->controls($model) ?>

<?php ActiveForm::end() ?>
