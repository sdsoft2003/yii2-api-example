<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Request;
use api\modules\v1\Enums\RequestEnum;
use yii\rest\ActiveController;
use Yii;

/**
 * Request Controller API
 *
 * @author Denis Kabanov<sdsoft2003@gmail.com>
 */
class RequestController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Request';

    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'POST', 'PUT', 'DELETE'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['options']);
        return $actions;
    }

    public function actionIndex()
    {
        $method = Yii::$app->request->getMethod();
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }
        $data =[];
        $model = new Request();

       switch ($method) {
           case 'GET':
               $filter = '';
               if (isset($requestParams['filter']) && !empty($requestParams['filter']) && strtolower($requestParams['filter']) === 'resolved') $filter = RequestEnum::RESOLVED;
               if (isset($requestParams['filter']) && !empty($requestParams['filter']) && strtolower($requestParams['filter']) === 'active') $filter = RequestEnum::ACTIVE;
               $data = $model->getRequests($requestParams['id'] ?? null, $filter);
               break;
           case 'POST':
               if ((!isset($requestParams['name']) || empty($requestParams['name'])) || (!isset($requestParams['email']) || empty($requestParams['email']))
                   || (!isset($requestParams['message']) || empty($requestParams['message']))) {
                   return ['success' => false, 'error' => 'Для обработки задачи требуется имя и email пользователя, а также сообщение', 'param' => $requestParams, 'method' => $method];
               } else {
                   $data = $model->postRequests($requestParams);
               }
               break;
           case 'PUT':
               if ((!isset($requestParams['id']) || empty($requestParams['id'])) || (!isset($requestParams['comment']) || empty($requestParams['comment']))) {
                   return ['success' => false, 'error' => 'Для ответа на конкретную задачу требуется id задачи и заполненый комментарий', 'param' => $requestParams, 'method' => $method];
               } else {
                   $data = $model->putRequests($requestParams ?? []);
               }
               break;
           case 'DELETE':
               if (!isset($requestParams['id']) || empty($requestParams['id'])) {
                   return ['success' => false, 'error' => 'Для удаления требуется id записи', 'param' => $requestParams, 'method' => $method];
               } else {
                   $data = $model->delRequests($requestParams['id']);
               }
               break;
       }
        if ($data['errorCode'] === 0) {
            return ['success' => !((bool)$data['errorCode']), 'data' => $data['data']];
        } else {
            return ['success' => !((bool)$data['errorCode']), 'error' => $data['errorText'], 'param' => $requestParams, 'method' => $method];
        }
    }

}