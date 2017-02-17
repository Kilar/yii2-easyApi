<?php
namespace backend\module\rbac\controllers;

use Yii;
use yii\helpers\Html;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use backend\module\rbac\models\AuthMenu;
use backend\module\rbac\service\MenuService;
/**
 * 菜单列表管理模块
 * @author Yong
 *
 */
class  MenuController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthMenu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthMenu();
        $searchModel->formName = 'AuthMenuS';
        $filter = self::safeValidate($searchModel);
        $data = MenuService::index($searchModel, $filter);
        $data['searchModel'] = $searchModel;
        return $this->render('index', $data);
    }

    /**
     * Displays a single AuthMenu model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $data = MenuService::view($model);
        $viewVars = [
            'model' => $model,
            'pMenuName' => $data['pMenuName'],
            'levelCnameList' => $data['levelCnameList'],
        ];
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "菜单 #".$model->name,
                'content'=>$this->renderAjax('view', $viewVars),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        }else{
            return $this->render('view', $viewVars);
        }
    }

    /**
     * Creates a new AuthMenu model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $viewVars = [
            'preLevelMenu' => [],
            'model' => ($model = new AuthMenu()),
            'levelCnameList' => MenuService::getLevelCName(1111),
        ];
        $model->addRules([
            [['name', 'level'], 'required'],
            ['created_at', 'default', 'value' => time()],
            ['pid', 'required', 'when' => [MenuService::class, 'isLevelMoreThanOne']],
            ['uri', 'required', 'when' => [MenuService::class, 'isLevelMoreThanTwo']],
            ['sort', 'required', 'when' => function($model){ return $model->level < 4; }],
            ['sort', 'default', 'value' => 0, 'when' => function($model){ return $model->level == 4; }],
            ['name', 'unique', 'targetClass' => AuthMenu::className(), 'message' => '{attribute}已经存在'],
        ]);
        $request = Yii::$app->getRequest();
        if ($request->isAjax) {
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title'=> "添加新菜单操作",
                    'content'=>$this->renderAjax('create', $viewVars),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
                        .Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            } else if($model->load($request->post()) && $model->validate() && MenuService::saveMenu($model, false)) {
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new AuthMenu",
                    'content'=>'<span class="text-success">Create AuthMenu success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            } else {
                return [
                    'title'=> "添加新菜单操作",
                    'content'=>$this->renderAjax('create', $viewVars),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }
        } else {
            // Process for non-ajax request
            if ($model->load($request->post()) && $model->validate() && MenuService::saveMenu($model, false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', $viewVars);
            }
        }

    }

    /**
     * Updates an existing AuthMenu model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = MenuService::getUpdateNData($model);
        $viewVars = [
            'model' => $model,
            'pMenuName' => $data['pMenuName'],
            'preLevelMenu' => $data['preLevelMenu'],
            'levelCnameList' => $data['levelCnameList'],
        ];
        $model->addRules([
            [['name', 'level'], 'required'],
            ['pid', 'required','when' => [MenuService::class, 'isLevelMoreThanOne']],
            ['uri', 'required','when' => [MenuService::class, 'isLevelMoreThanTwo']],
            ['sort', 'required', 'when' => function($model){ return $model->level < 4; }],
            ['name', 'unique', 'targetClass' => AuthMenu::className(), 'message' => '{attribute}已经存在'],
        ]);
        $request = Yii::$app->request;
        if($request->isAjax){
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "修改菜单操作 #".$model->name,
                    'content'=>$this->renderAjax('update', $viewVars),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post()) && $model->validate() && MenuService::saveMenu($model, false)){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "AuthMenu #".$model->name,
                    'content'=>$this->renderAjax('view', $viewVars),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }else{
                 return [
                    'title'=> "修改菜单操作  #".$model->name,
                    'content'=>$this->renderAjax('update', $viewVars),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }
        }else{
            // Process for non-ajax request
            if ($model->load($request->post()) && $model->validate() && MenuService::saveMenu($model, false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', $viewVars);
            }
        }
    }

    /**
     * Delete an existing AuthMenu model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(Yii::$app->request->isAjax && MenuService::delete($this->findModel($id))){
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => false,'forceReload' => '#crud-datatable-pjax'];
        }else{
            // Process for non-ajax request
            return $this->redirect(['index']);
        }
    }

     /**
     * Delete multiple existing AuthMenu model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array or selected records primary keys
        if($request->isAjax && ($msg = MenuService::bulkDelete($pks))){
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [//'forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'
                'forceReload'=>'#crud-datatable-pjax',
                'title'=> "批量删除结果",
                'content'=>'<span class="text-success">'.$msg.'</span>',
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-right','data-dismiss'=>"modal"])
                 //.Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        }else{
            // Process for non-ajax request
            return $this->redirect(['index']);
        }

    }

    /**
     * Finds the AuthMenu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AuthMenu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthMenu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetMenu()
    {
        $request = Yii::$app->getRequest();
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $level = $request->post('level');
            $preLevelMenu = AuthMenu::getLevelMenu($level-1);
            return array_column($preLevelMenu, 'name', 'id');
        }
    }
}
