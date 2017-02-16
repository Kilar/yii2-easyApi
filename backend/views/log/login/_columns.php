<?php
// use yii\helpers\Url;
use \common\lib\helpers\Common;
return [
//     [
//         'class' => 'kartik\grid\CheckboxColumn',
//         'width' => '20px',
//     ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'user_id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'label'=>'用户名',
        'value' => function ($row) use ($userList) {
            return $userList[$row->user_id]['username'];
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'label'=>'用户角色',
        'value' => function ($row) use ($userRoleDesList) {
            return $userRoleDesList[$row->user_id] ?? '用户角色已经删除';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'ip',
        'value' => function ($row) {
            return Common::long2ip($row->ip);
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_at',
        'format' => 'datetime',
    ],
];
