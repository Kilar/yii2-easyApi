<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\module\rbac\models\AuthItem */
?>
<div class="auth-item-update">

    <?= $this->render('_form', [
        'model' => $model,
        'permissions' => $permissions,
    ]) ?>

</div>
