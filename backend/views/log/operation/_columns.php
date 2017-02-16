<?php
use common\lib\helpers\Common;
return [
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
        'attribute'=>'ip',
        'value' => function ($row) {
            return Common::long2ip($row->ip);
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
        'value' => function ($row) use ($typeList) {
            return $typeList[$row->type];
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'location',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'content',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_at',
        'format' => 'datetime',
    ],

];   
