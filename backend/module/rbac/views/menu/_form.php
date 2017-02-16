<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\module\rbac\models\AuthMenu */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs('
$(function() {
   $("#ajaxCrudModal").removeAttr("tabindex");
   $("#authmenu-pid").select2({width:"100%", placeholder:"请选择"});
});   

var getMenu = function(obj)
{
	var level = obj.value;
	$("#authmenu-pid").val("").trigger("change"); 
	if(level<=3){
		$(".field-authmenu-sort, .field-authmenu-params").removeClass("sr-only");
	}
	if(level == "" || level==1){
		$("#authmenu-pid option").remove();
		$(".field-authmenu-uri, .field-authmenu-params").addClass("sr-only");
		return;
	}else if(level == 2)
		$(".field-authmenu-uri, .field-authmenu-params").addClass("sr-only");
	else{
		$(".field-authmenu-uri").removeClass("sr-only");
		if(level == 4)
			$(".field-authmenu-sort").addClass("sr-only");
	}
	$.post("'. Url::to(['menu/get-menu']) .'" , {"level":level}, function(data){
		if($.isEmptyObject(data)){
			alert("系统异常");
		}
		var options = "<option value=\"\" selected=\"selected\">请选择</option>";
		for(var i in data){
			options += "<option value="+i+">"+data[i]+"</option>";
		}
		$("#authmenu-pid").html(options);
    }, "json")
}
');
?>
<div class="auth-menu-form">

    <?php $form = ActiveForm::begin(['id'=>'form']); ?>

    <?= $form->field($model, 'level')->dropDownList($levelCnameList, ['onchange' => 'getMenu(this)']) ?>
    
    <?= $form->field($model, 'pid')->dropDownList($preLevelMenu, ['prompt'=>'请选择']) ?>
    
    <?php
    if (empty($model->level) || $model->level<=3 ) {
        $class = 'form-group';
    } else {
        $class = 'form-group sr-only';
    }
    echo $form->field($model, 'sort',['options'=>['class' => $class]])->textInput(['maxlength' => true])->label('排序(从小到大排序)'); 
    ?>
   
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
    
    <?php 
    if (empty($model->level) || $model->level>=3 ) {
        $class = 'form-group';
    } else {
        $class = 'form-group sr-only';
    }
    echo $form->field($model, 'uri',['options'=>['class' => $class]])->textInput(['maxlength' => true]) 
    ?>
  
	<?php if (!Yii::$app->request->isAjax) { ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
</div>