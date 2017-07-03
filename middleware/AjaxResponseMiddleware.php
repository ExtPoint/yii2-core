<?php

namespace extpoint\yii2\middleware;

use extpoint\yii2\base\Controller;
use extpoint\yii2\traits\ISearchModelTrait;
use yii\base\ActionEvent;
use yii\base\Object;
use yii\web\Application;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class AjaxResponseMiddleware extends Object
{
    /**
     * @param Application $app
     */
    public static function register($app)
    {
        if ($app instanceof Application) {
            $app->on(Controller::EVENT_AFTER_ACTION, [static::className(), 'checkAjaxResponse']);
        }
    }

    /**
     * @param ActionEvent $event
     * @throws ForbiddenHttpException
     */
    public static function checkAjaxResponse($event)
    {
        $request = \Yii::$app->request;
        $response = \Yii::$app->response;

        $rawContentType = $request->contentType;
        if (($pos = strpos($rawContentType, ';')) !== false) {
            // e.g. application/json; charset=UTF-8
            $contentType = substr($rawContentType, 0, $pos);
        } else {
            $contentType = $rawContentType;
        }

        if ($contentType === 'application/json' && isset($request->parsers[$contentType])) {

            // Detect data provider
            if ($event->result instanceof ISearchModelTrait) {
                $data = $event->result->toFrontend();
            } else {
                $data = is_array($event->result) ? $event->result : [];
            }

            // Ajax redirect
            $location = $response->headers->get('Location');
            if ($location) {
                $data['redirectUrl'] = $location;
                $response->headers->remove('Location');
                $response->statusCode = 200;
            } else {
                // Flashes
                $session = \Yii::$app->session;
                $flashes = $session->getAllFlashes(true);
                if (!empty($flashes)) {
                    $data['flashes'] = $flashes;
                }
            }

            $response->format = Response::FORMAT_JSON;
            $event->result = $data;
        }
    }
}
