<?php
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $searchModel backend\module\rbac\models\AuthMenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '系统操作日志';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
?>

<div class="auth-menu-form">
<?= $this->render('_searchForm', [
        'searchModel' => $searchModel,
        'typeList' => $typeList,
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
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i> 操作日志列表',
                'before'=>'<em>* Resize table columns just like a spreadsheet by dragging the column edges.</em>
<script>
    date("#operationlogs-start_time");
    date("#operationlogs-end_time");
</script>
                ',
            ],
            'pager' => [
                'firstPageLabel' => '首页',
                'lastPageLabel'  => '尾页',
            ]
        ])?>
    </div>
</div>


