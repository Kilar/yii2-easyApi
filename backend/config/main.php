<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'defaultRoute' => 'home',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'class' => 'backend\lib\components\User',
            'identityClass' => 'backend\models\User',
            'loginUrl' => ['home/login'],
            'returnUrl' => '/',
            'check' => true,
            'authTimeout' => 7200,
            'admin' => [ 'admin' ], //超级用户角色ID
//             'enableAutoLogin' => true,
//             'identityCookie' => [
//                 'name' => '_identity-backend', 
//                 'httpOnly' => true,
//                 'domain' => '.yii.ee',
//             ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'maxLogFiles' => 20, // 最多20个日志文件
                    'maxFileSize' => 1024 * 100, // 最多100M
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/'.date('Y-m-d').'.log',
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'home/error',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'cache' => 'cache',
            'defaultRoles' => [
                'admin'
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'session' => [
//             'sessionCollection' => 'b_session',
//             'class' => '\yii\mongodb\Session',
            'class' => '\yii\redis\Session',
            'name' => 'yii-backend',
            'timeout' => 7200,
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,   //一定不要发布该资源
                    'js' => [
                        '//cdn.bootcss.com/jquery/2.1.4/jquery.min.js',
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
	'modules' => [
        'gridview' =>  [
            'class' => 'kartik\grid\Module'
        ],
        'rbac' =>  [
            'class' => 'backend\module\rbac\Module',
        ],
	    'app' =>  [
	        'class' => 'backend\module\app\Module'
	    ],
    ],
    //rbac检验行为类
    'as rbac' => [
        'class' => 'backend\lib\behaviors\RbacControl',
        //不需要检查权限的操作
        'except' => [
            'home/*',
        ],
        'rule' => [
            'class' => 'backend\lib\extensions\rbac\RbacRule',
            'allow' => true,
            'ips' => [getenv('REMOTE_ADDR')],
        ],
    ],
];
