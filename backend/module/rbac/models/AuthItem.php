<?php

namespace backend\module\rbac\models;

use Yii;
use \common\models\BaseActiveRecord;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 */
class AuthItem extends BaseActiveRecord
{
    const ROLE = 1;
    const MENU = 2;

    public $permissions = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    public function fields()
    {
        $fields = parent::fields();
        array_push($fields, 'permissions');
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function attributeRules() 
    {
        $attributes = $this->attributes();
        array_push($attributes, 'permissions');
        return [
            [ $attributes, 'trim'],
            [['description', 'data'], 'string'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['name', 'rule_name', 'description'], 'string', 'max' => 64],
            ['name', 'match', 'pattern' => '/^\w+$/', 'message' => '{attribute}只可以是英文数字'],
            ['description', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}]+$/u', 'message' => '{attribute}只可以是中文'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => '角色ID',
            'type' => 'Type',
            'description' => '角色名',
            'rule_name' => '所用规则',
            'data' => 'Data',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'permissions' => '角色权限',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * 获取用户所有角色name值
     * @param int $userId
     * @param boolean $cached
     * @return array
     */
    public static function getRoleNameByUid(int $userId, bool $cached=true)
    {
        $cahe = Yii::$app->getCache();
        $cachekey = Yii::$app->params['cache_prefix']['rbac']['get_role_name_by_uid'].'_'.$userId;
        if($userId<1){
            return [];
        }elseif($cached && $cahe->get($cachekey)){
            return $cahe->get($cachekey);
        }
        $roleList = Yii::$app->getAuthManager()->getAssignments($userId) ;
        $roleList = array_keys($roleList);
        if($roleList && $cached){
            $cahe->set($cachekey, $roleList);
        }
        return $roleList;
    }

    /**
     * 获取所有角色描述
     * @return array
     */
    public static function getDeses()
    {
        $return = [];
        $auth = Yii::$app->getAuthManager();
        foreach ($auth->getRoles() as $v){
            $return[ $v->name ] = $v->description;
        }
        return $return;
    }

    /**
     * 获取用户所有角色描述
     * @param integer $userId
     * @param boolean $cached
     * @return array
     */
    public static function getDesByUid(int $userId, bool $cached=true)
    {
        $return = [];
        $cache = Yii::$app->getCache();
        $cachekey = Yii::$app->params['cache_prefix']['rbac']['get_des_by_uid'].'_'.$userId;
        if ($userId<1) {
            return $return;
        } elseif ($cached && ($return = $cache->get($cachekey))) {
            return $return;
        }
        $auth = \Yii::$app->getAuthManager();
        foreach ($auth->getRolesByUser($userId) as $v) {
            $return[$v->name] = $v->description;
        }
        if ($cached && $return) {
            $cache->set($cachekey, $return);
        }
        return  $return;
    }

    /**
     * 获取用户列表所有角色描述
     * @param array $userIdList
     * @param bool $cached
     * @return array
     */
    public static function getDesesByUids(array $userIdList, bool $cached=true)
    {
        $return = [];
        $userIdList = array_filter($userIdList);
        if (!$userIdList) {
            return  $return;
        }
        foreach ($userIdList as $uid) {
            if (!isset($return[$uid] )) {
                $return[$uid] = self::getDesByUid($uid, $cached);
            }
        }
        return  $return;
    }

    /**
     * 清除用户角色缓存
     * @param integer $userId
     */
    public static function delUserRoleCache(int $userId)
    {
        $nameKey = Yii::$app->params['cache_prefix']['rbac']['get_role_name_by_uid'].'_'.$userId;
        $desKey = Yii::$app->params['cache_prefix']['rbac']['get_des_by_uid'].'_'.$userId;
        $cache = \Yii::$app->getCache();
        if ($cache->exists($nameKey)) {
            $cache->delete($nameKey);
        }
        if ($cache->exists($desKey)) {
            $cache->delete($desKey);
        }
    }

    /**
     * 批量清除用户角色缓存
     * @param array $userIds
     */
    public static function bulkDelUserRoleCache(array $userIds)
    {
        foreach ($userIds as $userId) {
            self::delUserRoleCache((int) $userId);
        }
    }
}
