<?php
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $searchModel backend\module\rbac\models\AuthMenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '登录日志';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
?>

<div class="auth-menu-form">
<?= $this->render('_searchForm', [
        'searchModel' => $searchModel,
 ]) ?>
</div>

<div class="grid-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'pjax'=>true,
            'export' => false,
            'columns' => require(__DIR__.'/_columns.php'),
//             'toolbar'=> [
//                 ['content'=>
//                     Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
//                     ['role'=>'modal-remote','title'=> 'Create new Auth Menus','class'=>'btn btn-default']).
//                     Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
//                     ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
//                     '{toggleData}'.
//                     '{export}'
//                 ],
//             ],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i> 登录日志列表',
                'before'=>'<em>* Resize table columns just like a spreadsheet by dragging the column edges.</em>
<script>
    date("#loginlogs-start_time");
    date("#loginlogs-end_time");
</script>
                ',
//                 'after'=>BulkButtonWidget::widget([
//                             'buttons'=>Html::a('<i class="glyphicon glyphicon-trash"></i>&nbsp; Delete All',
//                                 ["bulk-delete"] ,
//                                 [
//                                     "class"=>"btn btn-danger btn-xs",
//                                     'role'=>'modal-remote-bulk',
//                                     'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
//                                     'data-request-method'=>'post',
//                                     'data-confirm-title'=>'Are you sure?',
//                                     'data-confirm-message'=>'Are you sure want to delete this item'
//                                 ]),
//                         ]).
//                         '<div class="clearfix">
//                              <script>
//                              </script>
//                          </div>',
            ],
            'pager' => [
                'firstPageLabel' => '首页',
                'lastPageLabel'  => '尾页',
            ]
        ])?>
    </div>
</div>


