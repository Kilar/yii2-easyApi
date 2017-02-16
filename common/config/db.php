<?php
use common\lib\helpers\SysHelper;
return  [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => SysHelper::getEnv('db_dsn', 'mysql:host=localhost;dbname=yii'),
        'username' => SysHelper::getEnv('db_username', 'root'),
        'password' => SysHelper::getEnv('db_password', ''),
        'tablePrefix' => SysHelper::getEnv('db_tablePrefix', ''),
    ],
    'redis' => [
        'class' => 'yii\redis\Connection',
        'hostname' => SysHelper::getEnv('redis_hostname', 'localhost'),
        'port' => SysHelper::getEnv('redis_port', '6379'),
        'database' => SysHelper::getEnv('redis_database', 0),
        'password' => SysHelper::getEnv('redis_password', 'yong123'),
    ],
    'mongodb' => [
        'class' => '\yii\mongodb\Connection',
        'dsn' => SysHelper::getEnv('mongo_dsn', 'mongodb://yong:yong123@localhost:27017/yii'),
    ],
];