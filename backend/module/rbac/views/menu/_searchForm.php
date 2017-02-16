<?php 
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->registerJs(
   '$("document").ready(function(){
        $("#menu").on("pjax:end", function() {
            $.pjax.reload({container:"#crud-datatable-pjax"});  //Reload GridView
        });
    });'
);
?>

<?php yii\widgets\Pjax::begin(['id' => 'menu']) ?>

    <?php $form = ActiveForm::begin(['method'=>'get','options'=>['class'=>'form-inline', 
            'style'=>'margin:5px 0 15px 0;padding:1% 0 7% 0', 'data-pjax' => true ]]); ?>
    
    <div class="form-group col-md-12">    
        <?= $form->field($searchModel, 'level',['options'=>['class'=>'form-group col-md-3']])->dropDownList($levelCnameList, ['prompt'=>'请选择']) ?>
            
        <?= $form->field($searchModel, 'pid',['options'=>
                ['class'=>'form-group col-md-3'], 'inputOptions' => 
                ['class' => 'form-control']])->dropDownList($menuList, ['prompt'=>'请选择']) ?>
        
        <?= $form->field($searchModel, 'name',['options'=>['class'=>'form-group col-md-3']])->textInput(['maxlength' => true]); ?>
    
        <?= $form->field($searchModel, 'uri',['options'=>['class' => 'form-group col-md-3']])->textInput(['maxlength' => true]) ?>
    </div>
    
    <div class="form-group col-md-12">
        <div class="form-group col-md-3 ">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::submitButton( 'search', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    
<?php yii\widgets\Pjax::end() ?>