<?php
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->registerJs(
   '$("document").ready(function(){
        $("#loginlogs").on("pjax:end", function() {
            $.pjax.reload({container:"#crud-datatable-pjax"});  //Reload GridView
        });
    });'
);
?>

<?php yii\widgets\Pjax::begin(['id' => 'loginlogs']) ?>

    <?php $form = ActiveForm::begin(['method'=>'get','options'=>['class'=>'form-inline',
            'style'=>'margin:5px 0 15px 0;padding:1% 0 7% 0', 'data-pjax' => true ]]); ?>

    <div class="form-group col-md-12">

        <?= $form->field($searchModel, 'user_id',['options'=>['class' => 'form-group col-md-3']])
        ->textInput(['maxlength' => true])->label('&nbsp;用户ID&nbsp;&nbsp;') ?>

        <div class="form-group col-md-4">
        <?= Html::activeLabel($searchModel, 'created_at')?>
        <?= Html::activeTextInput($searchModel, 'start_time', ['class' => 'form-control', 'style' => 'width:30%']) ?>
        --
        <?= Html::activeTextInput($searchModel, 'end_time', ['class' => 'form-control', 'style' => 'width:30%']) ?>
        </div>

        <?= $form->field($searchModel, 'type',['options'=>['class'=>'form-group col-md-3']])->dropDownList($typeList, ['prompt'=>'请选择']) ?>

        <div class="form-group col-md-2">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::submitButton( 'search', ['class' => 'btn btn-primary']) ?>
        </div>

    </div>

    <div class="form-group col-md-12">
        <?= $form->field($searchModel, 'location',['options'=>['class' => 'form-group col-md-3']])->textInput(['maxlength' => true]) ?>

        <?= $form->field($searchModel, 'content',['options'=>['class' => 'form-group col-md-3']])->textInput(['maxlength' => true]) ?>

        <?= $form->field($searchModel, 'ip',['options'=>['class' => 'form-group col-md-3']])->textInput(['maxlength' => true]) ?>
    </div>

    <?php ActiveForm::end(); ?>

<?php yii\widgets\Pjax::end() ?>