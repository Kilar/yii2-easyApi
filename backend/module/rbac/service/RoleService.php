<?php
namespace backend\module\rbac\service;

use Yii;
use yii\db\Connection;
use yii\db\Transaction;
use yii\data\ActiveDataProvider;
use backend\module\rbac\models\AuthItem;
use backend\module\rbac\models\AuthMenu;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use backend\models\OperationLog;
use yii\helpers\Json;
use backend\service\LogService;
class RoleService extends BaseService
{
    /**
     * 角色列表
     * @param AuthItem $model
     * @param bool $filter
     * @return \yii\data\ActiveDataProvider
     */
    public static function getIndexNData(AuthItem $model, bool $filter)
    {
        $query = $model::find();
        $dataProvider = new ActiveDataProvider([
                'query' => $query->where(['type' => AuthItem::ROLE]),
                'sort' => [
                    'defaultOrder' => [
                        'updated_at' => SORT_DESC,
                    ]
                ],
        ]);
        if ($filter) {
            $query->andFilterWhere(['name' => $model->name, 'description' => $model->description])
                ->andFilterWhere(['>', 'created_at', $model->created_at])
                ->andFilterWhere(['<', 'updated_at', $model->updated_at]);
        }
        return $dataProvider;
    }

    /**
     * 保存当前角色与权限
     * @param string $runValidation
     * @param string $attributeNames
     * @throws \Exception
     * @return boolean
     */
    public static function saveRole(AuthItem $model, $runValidation = false, $attributeNames = null)
    {
        $model->updated_at = time();
        if($model->permissions){ //权限列表字符串转换为数组
            $model->permissions = array_filter(explode(',', $model->permissions));
        }
        $connection = $model::getDb();
        $transaction = $connection->beginTransaction(Transaction::READ_COMMITTED);
        try{
            //日志类型
            $type = $model->isNewRecord ? OperationLog::CREATE : OperationLog::UPDATE;
            if(!$model->save($runValidation, $attributeNames)){
                throw new ServerErrorHttpException;
            }
            if($model->isNewRecord && $model->permissions){
                if(!self::savePermissions($model->name, $model->permissions)){
                    throw new ServerErrorHttpException;
                }
            }else{
                $auth = Yii::$app->getAuthManager();
                $oldPermissions = array_keys($auth->getChildren($model->name));
                if(!$model->permissions){
                    //移除角色所有权限
                    if($oldPermissions && !$auth->removeChildren($model)){
                        throw new ServerErrorHttpException;
                    }
                }elseif(array_diff($model->permissions, $oldPermissions) || array_diff($oldPermissions, $model->permissions)){
                    //修改角色权限
                    if($oldPermissions && !$auth->removeChildren($model)){
                        throw new ServerErrorHttpException;
                    }
                    if(!self::savePermissions($model->name, $model->permissions)){
                        throw new ServerErrorHttpException;
                    }
                }
            }
            //保存系统操作日志
            LogService::saveOperationLog($type, __METHOD__, Json::encode($model));
            $transaction->commit();
            $auth->invalidateCache(); //清除系统rbac缓存数据
            return true;
        }catch (\Exception $e){
            $transaction->rollBack();
            $model->addError('description', '系统异常');
            return false;
        }
    }

    /**
     * 保存角色对象菜单权限
     * @param string $roleName
     * @param array $permissions
     * @param Connection $connection
     * @return number
     */
    public static function savePermissions(string $roleName, array $permissions)
    {
        if(empty(($permissions = array_filter($permissions))) || empty($roleName)){
            return 0;
        }
        $rows = [];
        foreach ($permissions as $v){
            $row = [];
            $row['parent'] = $roleName;
            $row['child'] = $v;
            $rows[] = $row;
        }
        return \Yii::$app->getDb()->createCommand()
            ->batchInsert('{{%auth_item_child}}', array_keys($row), $rows)->execute();
    }

    /**
     * 获取某角色所有权限
     * @param string $role
     * @return array
     */
    public static function getPemissions(string $role = '')
    {
        $return = [];
        $auth = Yii::$app->getAuthManager();
        $rolePermissions = empty($role) ? null : $auth->getChildren($role);
        list ($level1, $level2, $level3, $level4) = AuthMenu::getAllMenu();
        foreach ($level4 as $v) {
            $action = [];
            if (!empty($role) && !empty($rolePermissions[$v['uri']])) {
                $action['state'] = ['checked' => true];
                $level3[$v['pid']]['state'] = ['checked' => true];
            }
            $action['text'] = $v['name'];
            $action['href'] = $v['uri'];
            $action['level'] = 4;
            $level3[$v['pid']]['nodes'][] = $action ;
        }
        foreach ($level3 as $v) {
            $item = [];
            if (!empty($role) && !empty($rolePermissions[$v['uri']])) {
                $item['state'] = ['checked'=>true];
                $level2[$v['pid']]['state'] = ['checked' => true];
                $level1[$level2[$v['pid']]['pid']]['state'] = ['checked' => true];
            } elseif (!empty($v['state'])) {
                $item['state'] = $v['state'];
                $level2[$v['pid']]['state'] = ['checked'=>true];
                $level1[$level2[$v['pid']]['pid']]['state'] = ['checked' => true];
            }
            $item['text'] = $v['name'];
            $item['href'] = $v['uri'];
            $item['level'] = 3;
            if (!empty($v['nodes'])) {
                $item['nodes'] = $v['nodes'];
            }
            $level2[$v['pid']]['nodes'][] = $item ;
        }
        foreach ($level2 as $v) {
            $menu = [];
            if (empty($v['nodes'])) {
                continue;
            } elseif(!empty($v['state'])) {
                $menu['state'] = $v['state'];
            }
            $menu['text'] = $v['name'];
            $menu['nodes'] = $v['nodes'];
            $level1[$v['pid']]['nodes'][] = $menu;
        }
        foreach ($level1 as $v) {
            if (empty($v['nodes'])) {
                continue;
            }
            $v['text'] = $v['name'];
            $v['state']['expanded'] = false;
            unset($v['name']);
            $return[] = $v;
        }
        return $return;
    }

    /**
     * 删除某个角色
     * @param AuthItem $model
     * @throws ForbiddenHttpException
     * @return boolean
     */
    public static function delete(AuthItem $model)
    {
        if ($model->isNewRecord) {
            throw new ForbiddenHttpException('禁止空模型执行删除操作');
        } elseif ($model->delete()) {
            //清除系统rbac缓存数据
            $auth = Yii::$app->getAuthManager();
            $auth->invalidateCache();
            LogService::saveOperationLog(OperationLog::DELETE, __METHOD__, '成功删除name值为' . $model->name . '的角色');
            return true;
        }
    }

    /**
     * 删除多个角色
     * @param array $idList
     * @throws ServerErrorHttpException
     * @return boolean
     */
    public static function bulkDelete(array $nameList)
    {
        $num = AuthItem::deleteAll(['name' => $nameList, 'type' => 1]);
        if ($num) {
            //清除系统rbac缓存数据
            $auth = Yii::$app->getAuthManager();
            $auth->invalidateCache();
            LogService::saveOperationLog(OperationLog::DELETE, __METHOD__,'成功删除name值为' . implode(',', $nameList) . '的' . $num . '个角色');
            return true;
        } else {
            throw new ServerErrorHttpException('服务器异常');
        }
    }

}
