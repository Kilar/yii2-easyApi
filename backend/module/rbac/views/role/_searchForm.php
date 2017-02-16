<?php 
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->registerJs(
   '$("document").ready(function(){
        $("#role").on("pjax:end", function() {
            $.pjax.reload({container:"#crud-datatable-pjax"});  //Reload GridView
        });
    });'
);
?>

<?php yii\widgets\Pjax::begin(['id' => 'role']) ?>
   
    <?php $form = ActiveForm::begin(['id'=>'form', 'method'=>'get','options'=>['class'=>'form-inline', 
            'style'=>'margin:5px 0 15px 0;padding:1% 0 3% 0', 'data-pjax' => true ]]); ?>
    
    <div class="form-group col-md-12">  
        <?= $form->field($searchModel, 'name',['options'=>['class'=>'form-group col-md-3']])->textInput(['maxlength' => true]); ?>
    
        <?= $form->field($searchModel, 'description',['options'=>['class' => 'form-group col-md-3']])->textInput(['maxlength' => true]) ?>
        
        <div class="form-group col-md-3">
            <?= Html::submitButton( 'search', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    
<?php yii\widgets\Pjax::end() ?>





