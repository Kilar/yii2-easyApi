<?php
namespace backend\module\app\service;

use yii\data\ActiveDataProvider;
use common\models\AppVersionRecord;
use backend\service\LogService;
use backend\models\OperationLog;
use yii\helpers\Json;
class VersionService extends BaseService 
{
    /**
     * 获取版本列表数据
     * @param \common\models\AppVersionRecord $model
     * @param bool $filter
     * @return \yii\data\ActiveDataProvider[]
     */
    public static function index($model, bool $filter) 
    {
        $return = [];
        
        $query = $model::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        if ($filter) {
            $query->andFilterWhere([
                'id' => $model->id,
                'platform' => $model->platform,
            ]);
            
            $query->andFilterWhere(['like', 'version_num', $model->version_num])
            ->andFilterWhere(['like', 'local_key', $model->local_key])
            ->andFilterWhere(['like', 'update_content', $model->update_content]);
        }
        
        $return['dataProvider'] = $dataProvider;
        $return['platformNames'] = self::getPlatformName();
        
        return $return;
    }
    
    /**
     * 获取接口平台名称
     * @param int $type
     * @return mixed
     */
    public static function getPlatformName(int $type = 0)
    {
        return \Yii::$app->params['api_plaltform'][$type] ?? \Yii::$app->params['api_plaltform'];
    }
    
    /**
     * 创建客户端版本操作
     * @param AppVersionRecord $model
     * @return boolean
     */
    public static function create(AppVersionRecord $model) 
    {
        $model->generateLocalKey();
        if (!$model->save()) {
            return false;
        }
        LogService::saveOperationLog(OperationLog::CREATE, __METHOD__, Json::encode($model));
        return true;
    }
    
    /**
     * 更新客户端版本操作
     * @param AppVersionRecord $model
     * @return boolean
     */
    public static function update(AppVersionRecord $model)
    {
        if (!$model->save()) {
            return false;
        }
        LogService::saveOperationLog(OperationLog::UPDATE, __METHOD__, Json::encode($model));
        return true;
    }
    
    /**
     * 删除一条客户端版本信息操作
     * @param AppVersionRecord $model
     * @return boolean
     */
    public static function delete(AppVersionRecord $model)
    {
        $model->delete();
        LogService::saveOperationLog(OperationLog::DELETE, __METHOD__, Json::encode($model));
        return true;
    }
    
    /**
     * 删除多条客户端版本信息操作
     * @param array $pks
     * @return boolean
     */
    public static function bulkDelete($pks)
    {
        foreach (AppVersionRecord::findAll(['id' => $pks]) as $model) {
            self::delete($model);
        }
    }
    
}