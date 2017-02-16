<?php

namespace app\models;

use yii\base\Model;

/**
 * 基础参数过滤器
 */
class ParamsFilter extends Model
{
    public $ver;
    public $time;
    public $sign;
    public $token;
    public $pf;

    /**
     * @var array 不需要检查token的路径
     */
    static $notNeedPaths = [
        'register', 'login'
    ];
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ver', 'time', 'sign', 'pf'], 'required'],
            ['token', 'required',  'when' => [$this , 'isNeedToken']],
            [['time', 'pf'], 'integer'],
            ['pf', 'in', 'range' => [1, 2]],
        ];
    }
    
    /**
     * 检查请求是否需要token
     */
    public function isNeedToken($model = null)
    {
        $path = \Yii::$app->getRequest()->pathInfo;
        
        $arr = array_flip(self::$notNeedPaths);
     
        return !isset($arr[$path]);
    }
    
    public function attributeLabels()
    {
        return [
            'pf' => '系统标识',
            'ver' => '版本号',
            'sign' => '验签',
            'time' => '请求时间',
            'token' => '授权码',
        ];
    }

  
  
}
