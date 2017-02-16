<?php
namespace common\lib\helpers;

use yii\base\Exception;
/**
 * 公共数据库操作类
 * @author yong
 */
class DbHelper
{
    /**
     * 获取组件某数据库连接类.
     * 如果没有设置，默认连接db数据库.
     * eg. 假如要使用createCommand()操作非db数据库db2, 以下即可
     * $connection = Common::getConnection('db2');
     * $sql = '..........' ;
     * $connection->createCommand($sql)->excute();
     * @param string $db db值为组件配置中数据库连接id
     * @return Connection <object>
     * @throws Exception
     */
    public static function getConnection(string $db = 'db') 
    {
        if (\Yii::$app->has($db) && ($connection = \Yii::$app->get($db)) instanceof \yii\db\Connection){
            return $connection;
        }else {
            throw new Exception('The function parameter must be a component database connection ID.');
        }
    }
}
