<?php 
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->registerJs(
   '$("document").ready(function(){
        $("#user").on("pjax:end", function() {
            $.pjax.reload({container:"#crud-datatable-pjax"});  //Reload GridView
        });
    });'
);
?>

<?php yii\widgets\Pjax::begin(['id' => 'user']) ?>
    
     <?php $form = ActiveForm::begin(['id'=>'form', 'method'=>'get','options'=>['class'=>'form-inline', 
                'style'=>'margin:5px 0 15px 0;padding:1% 0 7% 0', 'data-pjax' => true ]]); ?>
                
    <div class="form-group col-md-12">             
        <?= $form->field($searchModel, 'username', ['options'=>['class'=>'form-group col-md-3'], 'inputOptions'=>['class'=>'form-control', 'id'=>'username']])->textInput() ?>
        
        <?= $form->field($searchModel, 'realname', ['options'=>['class'=>'form-group col-md-3'], 'inputOptions'=>['class'=>'form-control', 'id'=>'realname']])->textInput() ?>
    
        <?= $form->field($searchModel, 'email', ['options'=>['class'=>'form-group col-md-3'], 'inputOptions'=>['class'=>'form-control', 'id'=>'email']])->textInput() ?>
    
        <?= $form->field($searchModel, 'status', ['options'=>['class'=>'form-group col-md-3'], 'inputOptions'=>['class'=>'form-control', 'id'=>'status']])
                   ->dropDownList(Yii::$app->params['status'], ['prompt'=>'请选择']) ?>
    </div>
    
    <div class="form-group col-md-12">   
        <?= $form->field($searchModel, 'mobile', ['options'=>['class'=>'form-group col-md-3'], 'inputOptions'=>['class'=>'form-control', 'id'=>'mobile']])->textInput() ?>
        
        <?= $form->field($searchModel, 'role', ['options'=>['class'=>'form-group col-md-3'], 'inputOptions'=>['class'=>'form-control', 'id'=>'role']])->textInput() ?>
        
        <div class="form-group col-md-3">
            <?= Html::submitButton( 'search', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>    
    <?php ActiveForm::end(); ?>
    
<?php yii\widgets\Pjax::end() ?>