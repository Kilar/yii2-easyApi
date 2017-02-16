<?php
use app\lib\behaviors\RequestControl;
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-app',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-app', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the app
            'class' => '\yii\redis\Session',
            'name' => 'advanced-app',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'maxLogFiles' => 20, // 最多24个日志文件
                    'maxFileSize' => 1024 * 50, // 最多50M
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/'.date('Y-m-d').'.log',
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                ],
            ],
        ],
        //自定义异常处理类
        'errorHandler' => [
            'class' => 'app\lib\components\ErrorHandler',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'pattern' => 'test',
                    'route' => 'user/test',
                ],
                [
                    'pattern' => 'register',
                    'route' => 'user/register',
                ],
                [
                    'pattern' => 'login',
                    'route' => 'user/login',
                ],
            ],
        ],
    ],
    'params' => $params,
    //请求控制行为类，主要用于检查基础参数是否符合规则
    'as requestControl' => [
        'class' => RequestControl::className(),
        'underTime' => 864000, //请求时间限制
    ],
];
