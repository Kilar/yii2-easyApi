<?php
namespace app\lib\helpers;

use common\lib\interfaces\SignEncryption;
/**
 * api sign生成算法类
 * @author Yong
 *
 */
class ApiSign implements SignEncryption
{
    /**
     * 自定义生成验签算法
     * @param array $data
     * @param string $key
     * @return string
     */
    public static function generateSign($data, $key)
    {
        $data['key'] = $key;
        ksort($data);
        return md5(urldecode(http_build_query($data)));
    }
    
    
    
    
}