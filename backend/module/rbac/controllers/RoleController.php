<?php
namespace backend\module\rbac\controllers;

use Yii;
use yii\helpers\Html;
use \yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use backend\module\rbac\models\AuthItem;
use backend\module\rbac\service\RoleService;
class  RoleController extends BaseController
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
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItem();
        $searchModel->formName = 'Role';
        $filter = self::safeValidate($searchModel);
        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => RoleService::getIndexNData($searchModel, $filter),
        ]);
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "角色 #".$model->description,
                    'content'=>$this->renderAjax('view', [
                            'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                    Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                    'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new AuthItem model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $viewVars = [
            'model' => ($model = new AuthItem()),
            'permissions' => json_encode(RoleService::getPemissions()),
        ];
        $model->addRules([
            [['name', 'description'], 'required'],
            ['rule_name', 'default', 'value' => null],
            ['created_at', 'default', 'value' => time()],
            ['type', 'default', 'value' => AuthItem::ROLE],
            [['name', 'description'], 'unique', 'targetClass' => AuthItem::className(), 'message' => '{attribute}已经存在'],
        ]);
        $request = Yii::$app->getRequest();
        if ($request->isAjax) {
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                        'title'=> "新建角色",
                        'content'=>$this->renderAjax('create', $viewVars),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary sr-only','type'=>"submit",'id'=>'submit']).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"button", 'onclick'=>'return getChecked(1)'])
                ];
            } else if($model->load($request->post()) && $model->validate() && RoleService::saveRole($model)) {
                return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'title'=> "新建角色",
                        'content'=>'<span class="text-success">Create AuthItem success</span>',
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('创建更多',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            } else {
                return [
                        'title'=> "新建角色",
                        'content'=>$this->renderAjax('create', $viewVars),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary sr-only','type'=>"submit",'id'=>'submit']).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"button", 'onclick'=>'return getChecked(1)'])
                ];
            }
        } else {
            // Process for non-ajax request
            if ($model->load($request->post()) && $model->validate() && RoleService::saveRole($model)) {
                return $this->redirect(['view', 'id' => $model->name]);
            } else {
                return $this->render('create', $viewVars);
            }
        }
    }

    /**
     * Updates an existing AuthItem model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $viewVars = [
            'model' => ($model = $this->findModel($id)),
            'permissions' => json_encode(RoleService::getPemissions($model->name)),
        ];
        $model->addRules([
            [['name', 'description'], 'required'],
            ['type', 'default', 'value' => AuthItem::ROLE],
            ['rule_name', 'default', 'value' => null],
            [['name', 'description'], 'unique', 'targetClass' => AuthItem::className(), 'message' => '{attribute}已经存在'],
        ]);
        $request = Yii::$app->request;
        if ($request->isAjax) {
            //   Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                        'title'=> "修改角色 #".$model->description,
                        'content'=>$this->renderAjax('update', $viewVars),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary sr-only','type'=>'submit','id'=>'submit']).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>'button', 'onclick'=>'return getChecked(1)'])
                ];
            } else if ($model->load($request->post()) && $model->validate() && RoleService::saveRole($model)) {
                return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'title'=> "角色 #".$model->description,
                        'content'=>$this->renderAjax('view', [
                                'model' => $model,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }else{
                return [
                        'title'=> "修改角色信息  #".$model->description,
                        'content'=>$this->renderAjax('update', $viewVars),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary sr-only','type'=>"submit",'id'=>'submit']).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"button", 'onclick'=>'return getChecked(1)'])
                ];
            }
        }else{
            //  Process for non-ajax request
            if ($model->load($request->post()) && $model->validate() && RoleService::saveRole($model)) {
                return $this->redirect(['view', 'id' => $model->name]);
            } else {
                return $this->render('update', $viewVars);
            }
        }
    }

    /**
     * Delete an existing AuthItem model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (Yii::$app->request->isAjax && RoleService::delete($this->findModel($id))) {
            // Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        } else {
            // Process for non-ajax request
            return $this->redirect(['index']);
        }
    }

    /**
     * Delete multiple existing AuthItem model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionBulkDelete()
    {
        $request = Yii::$app->request;
        if ($request->isAjax &&
        RoleService::bulkDelete(explode(',', $request->post('pks')/*Array or selected records primary keys*/
        ))) {
            //  Process for ajax request
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        } else {
            //  Process for non-ajax request
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
