<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\module\rbac\models\AuthMenu */

?>
<div class="auth-menu-create">
    <?= $this->render('_form', [
        'model' => $model,
        'preLevelMenu' => $preLevelMenu,
        'levelCnameList' => $levelCnameList,
    ]) ?>
</div>
