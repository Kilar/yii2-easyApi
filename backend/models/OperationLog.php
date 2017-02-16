<?php
namespace backend\models;

use common\mongo\BaseActiveRecord;
/**
 * This is the model class for table "operation_log".
 *
 * @property string $_id
 * @property integer $ip
 * @property string $type
 * @property string $content
 * @property string $location
 * @property integer $user_id
 * @property integer $created_at
 */
class OperationLog extends BaseActiveRecord
{
    /**
     * @var string 日志类型
     */
    const CREATE = 1;
    const UPDATE = 2;
    const DELETE = 3;
    
    /**
     * @var string 时间搜索
     */
    public $start_time,$end_time;
    
    /**
     * @inheritdoc
     */
    public function attributeRules() :array
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
        return ['_id', 'ip','type', 'location', 'user_id', 'content', 'created_at'];
    }
    
    public function attributeLabels()
    {
        return [
            '_id' => 'id', 
            'ip' => '操作IP地址',
            'type' => '日志类型', 
            'location' => '操作方法', 
            'user_id' => '用户ID', 
            'content' => '日志内容', 
            'created_at' => '记录时间',
        ];
    }
}