<?php
namespace backend\module\rbac\controllers;

use Yii;
use yii\helpers\Html;
use \yii\web\Response;
use backend\models\User;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use backend\module\rbac\models\AuthItem;
use backend\module\rbac\service\UserService;
class  UserController extends BaseController
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new User();
        $searchModel->formName = 'UserS';
        $filter = self::safeValidate($searchModel, ['role']);
        $data = UserService::getIndexNData($searchModel, $filter);
        $data['searchModel'] = $searchModel;
        return $this->render('index', $data);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->role = UserService::getDesesByUserId($id);
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "用户 #".$model->username,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
        }else{
            return $this->render('view', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new User model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->addRules([
            ['created_at', 'default', 'value' => time()],
            ['status', 'default', 'value' => User::STATUS_ACTIVE],
            [['username', 'role', 'email', 'password', 'realname', 'mobile'], 'required'], //, 'role'
            [['username', 'email', 'mobile'], 'unique', 'targetClass' => User::className(), 'message' => '{attribute}已经存在'],
        ]);
        $request = Yii::$app->getRequest();
        if($request->isAjax){
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "创建用户",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post()) && $model->validate() && UserService::saveUser($model, false)){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "创建用户",
                    'content'=>'<span class="text-success">Create User success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])

                ];
            }else{
                return [
                    'title'=> "创建用户",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }
        }else{
            // Process for non-ajax request
            if ($model->load($request->post()) && $model->validate() && UserService::saveUser($model, false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }

    }

    /**
     * Updates an existing User model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->addRules([
            [['status', 'role'], 'required'],
        ]);
        $request = Yii::$app->getRequest();
        if($request->isAjax){
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "修改用户信息 #".$model->username,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post()) && $model->validate() && UserService::saveUser($model, false)){
                $model->role = UserService::getDesesByUserId($id);
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "用户 #".$model->username,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }else{
                 return [
                    'title'=> "修改用户信息 #".$model->username,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }
        }else{
            // Process for non-ajax request
            if ($model->load($request->post()) && $model->validate() && UserService::saveUser($model, false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing User model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if($request->isAjax && UserService::delete($model)){
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            // Process for non-ajax request
            return $this->redirect(['index']);
        }
    }

     /**
     * Delete multiple existing User model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array or selected records primary keys
        if($request->isAjax && UserService::bulkDelete($pks)){
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            //return ['forceClose'=>false,'forceReload'=>'#crud-datatable-pjax'];
            return [
                'forceReload'=>'#crud-datatable-pjax',
                'title'=> "批量删除结果",
                'content'=>'<span class="text-success">删除成功</span>',
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-right','data-dismiss'=>"modal"])
            ];
        }else{
            // Process for non-ajax request
            return $this->redirect(['index']);
        }

    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            $model->role = AuthItem::getRoleNameByUid($id);
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
