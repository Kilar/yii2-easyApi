<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppVersionRecord */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-version-record-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'api_version')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'app_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'platform')->dropDownList([1 => '安卓', 2 => 'IOS']) ?>
    
    <?= $form->field($model, 'fix_question')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'update_content')->textarea(['rows' => 6]) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
