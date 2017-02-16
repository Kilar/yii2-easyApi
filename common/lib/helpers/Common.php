<?php
namespace common\lib\helpers;

use Yii;
use yii\base\Model;
    
/**
 * 公共函数调用类
 * @author yong
 */
 class Common
{
    /**
     * 打印函数
     * @param mixed $var
     * @param boolean $isDie
     */
    public static function p($var, bool $isDie = true)
    {
        if (php_sapi_name() === 'cli'){
            print_r($var);
        }else{
            echo '<pre>';
            print_r($var);
        }

        if($isDie) die;
    }

    /**
     * 将IP地址转换为无符号十进制数字
     * @param string $ip
     * @return integer
     */
    public static function ip2long(string $ip) :int
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return -1;
        }
        return sprintf('%u', ip2long($ip));
    }

    /**
     * 将ip十进制数字转换回正确IP地址
     * @param int $longIp
     * @return string
     */
    public static function long2ip(int $longIp) :string
    {
        return long2ip($longIp);
    }

    /**
     * 获取真实客户端IP地址
     * @param boolean $toLong
     * @return string | integer
     */
    public static function getClientIp(bool $toLong = false)
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $toLong ? self::ip2long($ip) : $ip;
    }

    /**
     * 获取模型第一个错误信息
     * @param Model $model
     * @return string
     */
    public static function getModelFirstError($model)
    {
        return current(current($model->getErrors()));
    }

}