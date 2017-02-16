<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\module\rbac\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(['id'=>'role', 'options'=>['onsubmit'=>'return getCheckeds(false)']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput() ?>
    <div class="form-group field-authitem-tree">
         <label class="control-label" for="tree">角色权限分配</label>
         <div id='tree' >
        </div>
    </div>
    <?= $form->field($model, 'permissions', ['template'=>"{input}"])->hiddenInput() ?>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>                                     
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
<script type="text/javascript">
var defaultData = $.parseJSON('<?= $permissions ?>');
$(function() {
   checkboxTree('#tree', defaultData);
});

function getChecked(ajax)
{
	var checked = $('#tree').treeview('getChecked');
	if(!$.isEmptyObject(checked)){
		//获取所有选中的权限
		var permissions = '';
		for(var i in checked){
			if(checked[i].level == undefined)
				continue;
			permissions += ','+checked[i].href.trim();
		}
		$('#authitem-permissions').val(permissions.substr(1));
	}
// 	 else {
// 		alert('请选择权限');
// 		return;
// 	}
	//触发点击事件提交表单
    if(ajax)
	    $('#submit').trigger('click');
	return true;
}
</script>