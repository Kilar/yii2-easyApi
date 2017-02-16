<?php
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use backend\module\rbac\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs(
    '$(function() {
        $("#ajaxCrudModal").removeAttr("tabindex");
        $("#user-role").select2({width: "100%", placeholder: "请选择用户角色"});
    });
    $("#editPasswd").click(function(){
        $(".field-user-password").removeClass("sr-only");
    });'
);
$new = false;
$class = 'form-group sr-only';
?>
<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
	
	<?php 
	if(!$model->isNewRecord){ 
	    echo $form->field($model, 'status')->dropDownList(Yii::$app->params['status']) ;
	} 
	?>
	
	<div class="form-group field-user-role has-success">
	<label class="control-label" for="user-role">用户角色</label>
    <?= Html::activeDropDownList($model, 'role', AuthItem::getDeses(), ['multiple'=>'multiple']) ?>
	</div>
	
	<?php if($model->isNewRecord){ 
        $new = true;
        $class = 'form-group';
        ?>
    
    <?= $form->field($model, 'username')->textInput() ?>
    
    <?= $form->field($model, 'realname')->textInput() ?>
    
    <?= $form->field($model, 'mobile')->textInput() ?>
    
    <?= $form->field($model, 'email')->textInput() ?>
        
    <?php }else{ ?>
    
    <?=Html::button('修改密码', ['id'=>'editPasswd'])?>
        
    <?php } ?>
    
    <?= $form->field($model, 'password',['options'=>['class'=>$class]])->textInput() ?>
    
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
</div>
