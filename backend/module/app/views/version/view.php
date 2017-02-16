<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AppVersionRecord */
?>
<div class="app-version-record-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'api_version',
            'app_version',
            'platform',
            'local_key',
            'fix_question:ntext',
            'update_content:ntext',
        ],
    ]) ?>

</div>
