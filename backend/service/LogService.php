<?php
namespace backend\service;

use backend\models\LoginLog;
use \common\lib\helpers\Common;
use yii\data\ActiveDataProvider;
use backend\models\User;
use \common\lib\helpers\ArrayHelper;
use backend\models\OperationLog;
use yii\web\ForbiddenHttpException;
use backend\module\rbac\service\UserService;
use yii\web\ServerErrorHttpException;
/**
 *
 * @author Yong
 *
 */
class LogService extends BaseService
{
    /**
     * 用户登录日志列表
     * @param LoginLog $model
     * @param bool $filter 是否有进行过滤操作
     * @return \yii\db\ActiveRecord[][]|\yii\data\ActiveDataProvider[]
     */
    public static function loginLogList(LoginLog $model, bool $filter)
    {
        $return = [];
        $query = $model::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                     'created_at' => SORT_DESC,
                ]
            ],
        ]);

        if ($filter) {
            $query->filterWhere([
                    '_id' => $model->_id,
                    'ip' => $model->ip ? Common::ip2long($model->ip) : $model->ip,
                    'user_id' => $model->user_id ? (int) $model->user_id : $model->user_id ,
            ]);
            if ($model->start_time && $model->end_time) {
                $query->andFilterWhere(['between', 'created_at', strtotime($model->start_time), strtotime($model->end_time) + 86400]);
            } if ($model->end_time) {
                $query->andFilterWhere(['<=', 'created_at', strtotime($model->end_time) + 86400]);
            } elseif ($model->start_time) {
                $query->andFilterWhere(['>=', 'created_at', strtotime($model->start_time)]);
            }
        }

        $userIdList = ArrayHelper::arArrayColumn($dataProvider->getModels(), 'user_id', true);
        $return['userRoleDesList'] = UserService::getDesesByUserIds($userIdList); //用户角色描述列表
        $return['dataProvider'] = $dataProvider;
        $return['userList'] = User::getBYIds($userIdList); //用户信息列表
        return $return;
    }

    /**
     * 用户操作日志列表
     * @param OperationLog $model
     * @param bool $filter
     * @return \yii\db\ActiveRecord[][]|\backend\logic\string[][]|string[]|\yii\data\ActiveDataProvider[]
     */
    public static function operationLogList(OperationLog $model, bool $filter)
    {
        $return = [];
        $query = $model::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        if ($filter) {
            $query->filterWhere([
                    '_id' => $model->_id,
                    'location' => $model->location,
                    'type' => $model->type ? (int) $model->type : $model->type,
                    'ip' => $model->ip ? Common::ip2long($model->ip) : $model->ip,
                    'user_id' => $model->user_id ? (int) $model->user_id : $model->user_id,
            ]);
            $query->andFilterWhere(['like', 'content', $model->content]);
            if ($model->start_time && $model->end_time) {
                $query->andFilterWhere(['between', 'created_at', strtotime($model->start_time), strtotime($model->end_time) + 86400]);
            } if ($model->end_time) {
                $query->andFilterWhere(['<=', 'created_at', strtotime($model->end_time) + 86400]);
            } elseif ($model->start_time) {
                $query->andFilterWhere(['>=', 'created_at', strtotime($model->start_time)]);
            }
        }

        $userIdList = ArrayHelper::arArrayColumn($dataProvider->getModels(), 'user_id', true);
        $return['dataProvider'] = $dataProvider;
        $return['userList'] = User::getBYIds($userIdList); //用户信息列表
        $return['typeList'] = self::operationLogType2C(11111); //操作类型列表信息
        return $return;
    }

    /**
     * 保存用户登录日志信息
     * @return boolean
     */
    public static function saveLoginLog()
    {
        $logModel = new LoginLog();
        $logModel->created_at = time();
        $logModel->ip = Common::getClientIp(true);
        $logModel->user_id = (int) \Yii::$app->getUser()->getId();
        if ($logModel->save(false)) {
            return true;
        }
        throw new ServerErrorHttpException(current(current($logModel->errors)));
    }

    /**
     * 保存系统操作日志
     * @param integer $type
     * @param string $location
     * @param string $content
     * @throws ForbiddenHttpException
     * @return boolean
     */
    public static function saveOperationLog(int $type, string $location, string $content)
    {
        switch ($type) {
            case OperationLog::CREATE:
                break;
            case OperationLog::UPDATE:
                break;
            case OperationLog::DELETE:
                break;
            default:
                throw new ForbiddenHttpException('未识别操作');
        }
        $logModel = new OperationLog();
        $logModel->type = $type;
        $logModel->content = $content;
        $logModel->created_at = time();
        $logModel->location = $location;
        $logModel->ip = Common::getClientIp(true);
        $logModel->user_id = (int)\Yii::$app->getUser()->getId();
        if ($logModel->save(false)) {
            return true;
        }
        throw new ServerErrorHttpException(current(current($logModel->errors)));
    }

    /**
     * 操作日志转类型转中文
     * @param string $type
     * @return string[]|string
     */
    public static function operationLogType2C(int $type)
    {
        $typeList = [
            OperationLog::CREATE => '添加',
            OperationLog::UPDATE => '修改',
            OperationLog::DELETE => '删除',
        ];
        return $typeList[$type] ?? $typeList;
    }

}
