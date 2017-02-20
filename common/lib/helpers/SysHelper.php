<?php
namespace common\lib\helpers;

use yii\web\ServerErrorHttpException;
/**
 * 系统函数帮助类
 * @author hym 2016-11-20
 */
class SysHelper
{
    /**
     * @var string 默认cookie域名
     */
    static $ckDomain = '';
    
    /**
     * 设置某个cookie值
     * @param string $name
     * @param mix $value
     * @param array $options 可选设置项
     * @see \yii\web\Cookie
     */
    public static function setCookie(string $name, $value, array $options = [])
    {
        $cookies = \Yii::$app->getResponse()->getCookies();
        // 在要发送的响应中添加一个新的cookie
        $cookies->add(new \yii\web\Cookie([
            'name'     => $name,
            'value'    => $value,
            'expire'   => (int) ($options['expire'] ?? 0),
            'path'     => (string) ($options['path'] ?? '/'),
            'secure'   => (bool) ($options['secure'] ?? false),
            'httpOnly' => (bool) ($options['httpOnly'] ?? true),
            'domain'   => (string) ($options['domain'] ?? self::$ckDomain),
        ]));
    }

    /**
     * 获取某个已经设置的cookie值
     * @param string $name
     * @return \yii\web\Cookie
     * @see \yii\web\CookieCollection::get()
     */
    public static function getCookie(string $name)
    {
        return \Yii::$app->getRequest()->getCookies()->get($name);
    }

    /**
     * 删除某个cookie值
     * @param string $name
     * @param bool $removeFromBrowser
     */
    public static function removeCookie($cookie, bool $removeFromBrowser = true)
    {
        \Yii::$app->getResponse()->getCookies()->remove($cookie);
    }

    /**
     * 入口文件设置系统环境变量
     */
    public static function putEnvs()
    {
        $file = dirname(dirname(dirname(__DIR__))) . '/.env';
        if (!file_exists($file)) {
            throw new ServerErrorHttpException('缺少.env配置文件');
        }
        $contents = explode("\n", file_get_contents($file));
        foreach ($contents as $str) {
            if (strpos($str, '=') > 0) {
                putenv(trim($str));
            }
        }
    }
    
    /**
     * 获取环境变量
     * @param string $varName
     * @param string $default
     * @return string 
     */ 
    public static function getEnv(string $varName, string $default = null)
    {
        $val = getenv($varName);
        
        switch ($val) {
            case 'false':
                return false;
            case 'true':
                return true;
        }
        
        return $val ? $val : $default;
    }

}