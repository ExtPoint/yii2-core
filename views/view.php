<?php

namespace app\views;

use app\core\widgets\CrudControls;
use extpoint\yii2\base\CrudController;
use extpoint\yii2\base\Model;
use extpoint\yii2\widgets\DetailView;
use yii\web\View;

/* @var $this View */
/* @var $model Model */

/** @var CrudController $controller */
$controller = $this->context;

/** @var array $meta */
$meta = $controller::meta();

?>
<h1><?= \Yii::$app->siteMap->getTitle() ?></h1>

<?= CrudControls::widget([
    'model' => $model,
    'actionParams' => $controller::getMetaUrlParams(),
]) ?>

<?= DetailView::widget([
    'model' => $model,
    'controllerMeta' => $meta,
]) ?>
