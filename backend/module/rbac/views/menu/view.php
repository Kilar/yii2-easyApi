<?php

use yii\widgets\DetailView;
use backend\module\rbac\models\AuthMenu;

/* @var $this yii\web\View */
/* @var $model backend\module\rbac\models\AuthMenu */
?>
<div class="auth-menu-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'pid',
                'value' => $pMenuName,
            ],
            'sort',
            'name',
            'created_at:datetime',
            [
               'attribute' => 'level',
               'value' => $levelCnameList[$model->level],
            ],
            [
               'attribute' => 'uri',
               'value' => $model->uri !== 'null' ? $model->uri : '',
            ],
        ],
    ]) ?>
</div>
