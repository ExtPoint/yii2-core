<?php

namespace extpoint\yii2\base;

use extpoint\yii2\widgets\ActiveForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\ForbiddenHttpException;

class CrudController extends Controller
{
    public $indexView = '@vendor/extpoint/yii2-core/views/index';
    public $updateView = '@vendor/extpoint/yii2-core/views/update';
    public $viewView = '@vendor/extpoint/yii2-core/views/view';

    public static function meta()
    {
        return [];
    }

    public static function coreMenuItems($prefix = null)
    {
        $meta = static::meta();
        $prefix = $prefix ?: ArrayHelper::getValue($meta, 'url');

        /** @var Model $modelClass */
        $modelClass = static::getModelClass();
        $idName = $modelClass::getRequestParamName();

        $controllerId = static::getControllerId();

        $items = [];
        if (ArrayHelper::getValue($meta, 'createActionCreate')) {
            $items['create'] = [
                'label' => \Yii::t('app', 'Добавление'),
                'url' => static::generateRoute('create'),
                'urlRule' => $prefix . '/create',
            ];
        }
        if (ArrayHelper::getValue($meta, 'createActionUpdate')) {
            $items['update'] = [
                'label' => \Yii::t('app', 'Редактирование'),
                'url' => static::generateRoute('update'),
                'urlRule' => $prefix . '/<' . $idName . ':\d+>/update',
            ];
        }
        if (ArrayHelper::getValue($meta, 'createActionView')) {
            $items['view'] = [
                'label' => \Yii::t('app', 'Просмотр'),
                'url' => static::generateRoute('view'),
                'urlRule' => $prefix . '/<' . $idName . ':\d+>',
                'modelClass' => $modelClass::className(),
            ];
        }

        if (ArrayHelper::getValue($meta, 'createActionIndex')) {
            if (ArrayHelper::getValue($meta, 'withDelete')) {
                $items['delete'] = [
                    'url' => static::generateRoute('delete'),
                    'urlRule' => $prefix . '/<' . $idName . ':\d+>/delete',
                ];
            }

            return [
                $controllerId => [
                    'label' => ArrayHelper::getValue($meta, 'title'),
                    'url' => static::generateRoute('index'),
                    'urlRule' => $prefix,
                    'roles' => ArrayHelper::getValue($meta, 'roles'),
                    'items' => $items,
                ],
            ];
        } else {
            $result = [];
            foreach ($items as $key => $item) {
                $result[$controllerId . '_' . $key] = $item;
            }
            return $result;
        }
    }

    public function actionIndex()
    {
        /** @var FormModel $modelClass */
        $formModelClass = ArrayHelper::getValue(static::meta(), 'formModelClassName');

        $searchModel = null;
        if ($formModelClass) {
            $searchModel = new $formModelClass();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        } else {
            $modelClass = static::getModelClass();
            $dataProvider = new ActiveDataProvider([
                'query' => $modelClass::find(),
            ]);
        }

        return $this->render($this->indexView, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView()
    {
        $modelClass = static::getModelClass();
        $id = Yii::$app->request->get($modelClass::getRequestParamName());
        $model = $modelClass::findOrPanic($id);

        if (!$model->canView(Yii::$app->user->model)) {
            throw new ForbiddenHttpException();
        }

        return $this->render($this->viewView, [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $modelClass = static::getModelClass();

        /** @var Model $model */
        $model = new $modelClass();
        $model->attributes = static::getMetaUrlParams();

        if ($model->load(Yii::$app->request->post()) && $model->canCreate(Yii::$app->user->model) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Запись добавлена'));
            return $this->redirect(
                static::generateRoute('view', [$modelClass::getRequestParamName() => $model->primaryKey])
            );
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render($this->updateView, [
            'model' => $model,
        ]);
    }

    public function actionUpdate()
    {
        $modelClass = static::getModelClass();
        $id = Yii::$app->request->get($modelClass::getRequestParamName());
        $model = $modelClass::findOrPanic($id);

        if ($model->load(Yii::$app->request->post()) && $model->canUpdate(Yii::$app->user->model) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Запись обновлена'));
            return $this->redirect(
                static::generateRoute('view', [$modelClass::getRequestParamName() => $model->primaryKey])
            );
        }
        if (Yii::$app->request->isAjax) {
            return ActiveForm::renderAjax($model);
        }

        return $this->render($this->updateView, [
            'model' => $model,
        ]);
    }

    public function actionDelete()
    {
        $modelClass = static::getModelClass();
        $id = Yii::$app->request->get($modelClass::getRequestParamName());
        $model = $modelClass::findOrPanic($id);

        if ($model->canDelete(Yii::$app->user->model)) {
            $model->deleteOrPanic();
        } else {
            throw new ForbiddenHttpException();
        }
        return $this->redirect(static::generateRoute('index'));
    }

    /**
     * @return Model
     */
    public static function getModelClass()
    {
        return ArrayHelper::getValue(static::meta(), 'modelClassName');
    }

    /**
     * @return array
     */
    public static function getMetaUrlParams()
    {
        $url = ArrayHelper::getValue(static::meta(), 'url');
        preg_match_all('/<([^:>]+)[:>]/', $url, $matches);

        $values = [];
        foreach (ArrayHelper::getValue($matches, 1, []) as $name) {
            $value = Yii::$app->request->get($name);
            if ($value !== null) {
                $values[$name] = $value;
            }
        }
        return $values;
    }

    /**
     * @return string
     */
    public static function getControllerId() {
        preg_match('/([^\\\\]+)Controller(Meta)?$/', static::className(), $match);
        return Inflector::camel2id($match[1]);
    }

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public static function generateRoute($action, $params = [])
    {
        // Get module id
        $namespace = preg_replace('/\\\\[^\\\\]+$/', '', static::className());
        $namespace = preg_replace('/^app\\\\/', '', $namespace);
        $namespace = preg_replace('/\\\\controllers(\\\\meta)?$/', '', $namespace);
        $moduleId = str_replace('\\', '/', $namespace);

        // Get controller id
        $controllerId = static::getControllerId();

        // Append url params
        $params = array_merge($params, static::getMetaUrlParams());

        // Generate route
        $route = '/' . $moduleId . '/' . $controllerId . ($action ? '/' . $action : '');
        return array_merge([$route], $params);
    }
}
