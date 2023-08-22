<?php

namespace api\modules\v1\models;

use api\modules\v1\Enums\RequestEnum;
use yii\db\Expression;
use \yii\db\ActiveRecord;
/**
 * Request Model
 *
 * @property integer id Уникальный идентификатор
 * @property string name Имя пользователя - строка, обязательная
 * @property string email Email пользователя - строка, обязательная
 * @property int status Статус - enum(“Active”, “Resolved”)
 * @property string message Сообщение пользователя - текст, обязательный
 * @property string created_at Время создания заявки - timestamp или datetime
 * @property string updated_at Время ответа на заявку
 */
class Request extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yii2advanced.request';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * Define rules for validation
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'email', 'comment', 'message'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Уникальный идентификатор',
            'name' => 'Имя пользователя - строка, обязательная',
            'email' => 'Email пользователя - строка, обязательная',
            'status' => 'Статус - enum(“Active”, “Resolved”)',
            'message' => 'Сообщение пользователя - текст, обязательный',
            'created_at' => 'Время создания заявки - timestamp или datetime',
            'updated_at' => 'Время ответа на заявку',
        ];
    }
    public function getRequests($id = null, $filter):array
    {
        if (!empty($id)){
            if (ctype_digit($id)) {
                $data = self::find()->select( ['id', 'name', 'email', new Expression('case status when 1 then \'active\' when 2 then \'resolved\' end as status'), 'message', 'comment', 'created_at', 'updated_at'])
                    ->where(['id'=>$id])->asArray()->one();
                return ['errorCode' => 0, 'data' => $data];
            } else {
                return ['errorCode' => 1, 'errorText' => 'Номера заявок имеют только цифры'];
            }
        }
        $data = self::find()->select( ['id', 'name', 'email', new Expression('case status when 1 then \'active\' when 2 then \'resolved\' end as status'), 'message', 'comment', 'created_at', 'updated_at']);
        if (!empty($filter)) {
            $data->andFilterWhere(['status'=>$filter]);
        }
        return ['errorCode' => 0, 'data' => $data->asArray()->All()];
    }

    public function putRequests($params = []):array
    {
        if (ctype_digit($params['id'])) {
            $model = self::find()->where(['id' => $params['id']])->one();
            if (empty($model)) return ['errorCode' => 1, 'errorText' => 'Задача с номером '.$params['id']. ' не существует.'];
            $model->comment = $params['comment'];
            $model->status = RequestEnum::RESOLVED;
            $model->updated_at = date('Y-m-d H:i:s', time());
            if (!$model->save(false)) return ['errorCode' => 1, 'errorText' => 'Ошибка записи задачи с номером '.$params['id']];
            return ['errorCode' => 0, 'data' => 'ok'];
        } else {
            return ['errorCode' => 1, 'errorText' => 'Номера заявок имеют только цифры'];
        }
    }

    public function delRequests($id = null):array
    {
        if (!empty($id)){
            if (ctype_digit($id)) {
                $model = self::find()->where(['id' => $id])->one();
                if (empty($model)) return ['errorCode' => 1, 'errorText' => 'Задача с номером '.$id. ' не существует. удалить не возможно'];
                $model->delete();
                return ['errorCode' => 0, 'data' => 'удалена'];
            } else {
                return ['errorCode' => 1, 'errorText' => 'Номера заявок имеют только цифры'];
            }
        }
        return ['errorCode' => 1, 'errorText' => 'Номера заявок не может быть пустым'];
    }

    public function postRequests($params = []):array
    {
        $model = new Request();
        $model->name = $params['name'];
        $model->email = $params['email'];
        $model->message = $params['message'];
        $model->status = RequestEnum::ACTIVE;
        $model->created_at = date('Y-m-d H:i:s', time());
        if (!$model->save()) return ['errorCode' => 1, 'errorText' => 'Ошибка записи задачи'];
        return ['errorCode' => 0, 'data' => 'Задача с номером '.$model->id.' создана.'];
    }


}

