<?php

use yii\widgets\DetailView;
use common\models\Common;
use backend\module\rbac\models\Role;
use backend\module\rbac\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model backend\models\User */
?>
<div class="user-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'status',
                'value' => Yii::$app->params['status'][$model->status],
            ],
            'id',
            'username',
            'realname',
            'mobile',
            'email:email',
            'role',
            'created_at:datetime',
            'updated_at:datetime',
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
        ],
    ]) ?>

</div>
