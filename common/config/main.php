<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
	'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'components' => [
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],
    ],
];
