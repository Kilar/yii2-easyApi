<?php
namespace backend\module\rbac\service;

use Yii;
use yii\db\Connection;
use yii\db\Transaction;
use backend\models\User;
use yii\data\ActiveDataProvider;
use backend\module\rbac\models\AuthItem;
use yii\web\ForbiddenHttpException;
use common\lib\helpers\Status;
use yii\web\ServerErrorHttpException;
use backend\models\OperationLog;
use yii\helpers\Json;
use backend\service\LogService;
/**
 *
 * @author Yong
 *
 */
class UserService extends BaseService
{
    /**
     * 用户列表数据
     * @param User $model
     * @param bool $filter
     * @return \yii\data\ActiveDataProvider[]
     */
    public static function getIndexNData(User $model, bool $filter)
    {
        $return = [];
        $query = $model::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'status' => SORT_ASC,
                    'updated_at' => SORT_DESC
                ]
            ],
        ]);
        $defaulShowStatus = [Status::ACTIVE, Status::STOP];
        $query->where(['status' => is_numeric($model->status) ? $model->status : $defaulShowStatus]);
        if ($filter) {
            $ids = $model::getUidsByDesKword($model->role);
            if ($model->id || ($model->role && !$ids)) {
                array_push($ids, $model->id);
            }
            $query->andFilterWhere([
                    'id' => $ids,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
            ]);
            $query->andFilterWhere(['like', 'username', $model->username])
                ->andFilterWhere(['like', 'auth_key', $model->auth_key])
                ->andFilterWhere(['like', 'password_hash', $model->password_hash])
                ->andFilterWhere(['like', 'password_reset_token', $model->password_reset_token])
                ->andFilterWhere(['like', 'mobile', $model->mobile])
                ->andFilterWhere(['like', 'realname', $model->realname])
                ->andFilterWhere(['like', 'email', $model->email]);
        }
        $userIdList = $dataProvider->getKeys();
        $return['dataProvider'] = $dataProvider;
        $return['userRoleList'] = self::getDesesByUserIds($userIdList);
        return $return;
    }

    /**
     * 保存用户信息
     * @param User $model
     * @param string $runValidation
     * @param unknown $attributeNames
     * @throws \Exception
     * @return boolean
     */
    public static function saveUser(User $model, $runValidation = false, $attributeNames = null)
    {
        $connection = $model::getDb();
        $transaction = $connection->beginTransaction(Transaction::READ_COMMITTED);
        try {
            //当前操作类型
            $type = $model->isNewRecord ? OperationLog::CREATE : OperationLog::UPDATE ;
            $model->updated_at = time();
            if ($model->isNewRecord) {
                $model->generateAuthKey();
                $model->setPassword($model->password);
                $roleList = $model->role;
            } else {
                $oldRole = AuthItem::getRoleNameByUid($model->id);
                $auth = Yii::$app->getAuthManager();
                //处理用户角色
                if (array_diff($model->role, $oldRole) || array_diff($oldRole, $model->role) ) {
                    //删除用户旧角色
                    if($oldRole && !$auth->revokeAll($model->id)){
                        throw new ServerErrorHttpException('系统异常');
                    }
                    $roleList = $model->role;
                }
                //修改密码
                if (!empty($model->password) || $model->status != $model->oldAttributes['status']) {
                    if(!empty($model->password)){
                        $model->setPassword($model->password);
                    }
                    if(!$model->save($runValidation, $attributeNames) ){
                        throw new ServerErrorHttpException('系统异常');
                    }
                }
            }
            if (!$model->save($runValidation, $attributeNames)) {
                throw new ServerErrorHttpException('系统异常');
            } else if (!empty($roleList) && !self::saveRole($model->id, $roleList, $connection)) {
                throw new ServerErrorHttpException('系统异常');
            }
            //记录日志
            LogService::saveOperationLog($type, __METHOD__, Json::encode($model));
            $transaction->commit();
            if ($type === OperationLog::UPDATE) { //清除用户角色缓存
                AuthItem::delUserRoleCache($model->id);
            }
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $model->addError('role', '系统异常:'.$e->getMessage());
            return false;
        }
    }

    /**
     * 保存用户角色
     * @param int $userId
     * @param array $roleList
     * @param Connection $connection
     * @return number
     */

    public static function saveRole(int $userId, array $roleList, Connection $connection)
    {
        if ($roleList) {
            $roleList = array_filter($roleList);
        } else {
            return 0;
        }
        $rows = [];
        $now  = time();
        foreach ($roleList as $v){
            $row = [];
            $row['item_name'] = $v;
            $row['user_id'] = $userId;
            $row['created_at'] = $now;
            $rows[] = $row;
        }
        return $connection->createCommand()
           ->batchInsert('{{%auth_assignment}}', array_keys($row), $rows)->execute();
    }

    /**
     * 删除用户
     * @param User $model
     * @throws ForbiddenHttpException
     * @return boolean
     */
    public static function delete(User $model)
    {
        if ($model->isNewRecord) {
            throw new ForbiddenHttpException('非法操作');
        }
        $model->status = User::STATUS_DELETED;
        $model->updated_at = time();
        if ($model->save(false)) {
            AuthItem::delUserRoleCache($model->id);
            LogService::saveOperationLog(OperationLog::DELETE, __METHOD__, '成功删除ID为'.$model->id.'的用户');
            return true;
        }
        return false;
    }

    /**
     * 批量删除多个用户
     * @param array $idList
     * @return boolean
     */
    public static function bulkDelete(array $idList)
    {
        $res = User::updateAll(
            ['status' => User::STATUS_DELETED, 'updated_at' => time()],
            ['id' => $idList]
         );
        if ($res) {
            AuthItem::bulkDelUserRoleCache($idList);
            LogService::saveOperationLog(OperationLog::DELETE, __METHOD__, '成功删除ID为'.implode(',', $idList).'的' . $res . '个用户');
            return true;
        }
        return false;
    }

    /**
     * 获取用户角色描述
     * @param int $userId
     * @return string
     */
    public static function getDesesByUserId(int $userId)
    {
        return implode(',', AuthItem::getDesByUid($userId));
    }

    /**
     * 获取多个用户角色描述
     * @param int $userId
     * @return string[] | []
     */
    public static function getDesesByUserIds(array $userIdList)
    {
        $return = [];
        foreach (AuthItem::getDesesByUids($userIdList) as $uid => $roleDeses) {
            if (!isset($return['userRoleDesList'][$uid]) && is_array($roleDeses)) {
                $return[$uid] = implode(',', $roleDeses);
            }
        }
        return $return;
    }
}
