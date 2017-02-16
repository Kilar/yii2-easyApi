<?php
namespace backend\models;

use common\mongo\BaseActiveRecord;
/**
 * This is the model class for table "login_log".
 *
 * @property string $_id
 * @property integer $ip
 * @property integer $user_id
 * @property integer $created_at
 */
class LoginLog extends BaseActiveRecord
{
    /**
     * @var string 时间搜索
     */
    public $start_time,$end_time;
    
    /**
     * @inheritdoc
     */
    public function attributeRules()
    {
        $attributes = $this->attributes();
        array_push($attributes, 'start_time', 'end_time');
        return [
            [$attributes, 'trim'],    
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return ['_id', 'user_id', 'ip', 'created_at'];
    }
    
    public function attributeLabels()
    {
        return [
            '_id' => 'id',
            'user_id' => '用户ID',
            'ip' => '用户登录IP',
            'created_at' => '登录时间',
        ];
        
    }
}