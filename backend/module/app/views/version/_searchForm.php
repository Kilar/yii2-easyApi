<?php 
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->registerJs(
   '$("document").ready(function(){
        $("#version").on("pjax:end", function() {
            $.pjax.reload({container:"#crud-datatable-pjax"});  //Reload GridView
        });
    });'
);
?>

<?php yii\widgets\Pjax::begin(['id' => 'version']) ?>

    <?php $form = ActiveForm::begin(['method'=>'get','options'=>['class'=>'form-inline', 
            'style'=>'margin:5px 0 15px 0;padding:1% 0 7% 0', 'data-pjax' => true ]]); ?>
    
    <div class="form-group col-md-12">    
        <?= $form->field($searchModel, 'api_version',['options'=>['class'=>'form-group col-md-3']])->textInput(['maxlength' => true]); ?>
        
        <?= $form->field($searchModel, 'app_version',['options'=>['class'=>'form-group col-md-3']])->textInput(['maxlength' => true]); ?>
    
        <?= $form->field($searchModel, 'local_key',['options'=>['class'=>'form-group col-md-4']])->textInput(['maxlength' => true]); ?>
    
        <?= $form->field($searchModel, 'platform',['options'=>['class' => 'form-group col-md-2']])->dropDownList($platformNames) ?>       
    </div>
    
    <div class="form-group col-md-12">
        <div class="form-group col-md-3 ">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::submitButton( 'search', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    
<?php yii\widgets\Pjax::end() ?>