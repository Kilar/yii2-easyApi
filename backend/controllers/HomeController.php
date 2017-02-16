<?php
namespace backend\controllers;

use Yii;
use \yii\web\Response;
use yii\filters\VerbFilter;
use backend\models\LoginForm;
use yii\filters\AccessControl;
use backend\service\HomeService;
use backend\service\LogService;
use backend\models\OperationLog;
class HomeController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'reset-password'],
                'rules' => [
                    [
                        'actions' => ['logout', 'reset-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'reset-password' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                //'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'height' => '40',
                'minLength' => '4',
                'maxLength' => '5',
             ],
       ];
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goBack();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->post()) && HomeService::login($model)) { 
            LogService::saveLoginLog();//保存登录日志信息
            return $this->goBack();
        } else {
            return $this->renderAjax('login', [
                'model' => $model,
                'catpcha' => HomeService::loginFail(),
            ]);
        }
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }
        
        $data = HomeService::index();
        return $this->renderPartial('index', $data);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        $response = \Yii::$app->getResponse();
        $response->format = $response::FORMAT_JSON;
        return '/';
    }

    public function actionTest()
    {
        return $this->render('test');
    }

    /**
     * 用户修改自己的密码
     */
    public function actionResetPassword()
    {
        $request = Yii::$app->getRequest();
        if (!$request->isAjax || !$request->isPost) {
            return $this->goBack();
        }
        $model = Yii::$app->getUser()->identity;
        $model->addRules([
             [['password', 'oldPassword'], 'required'],
        ]);
        $return = ['status' => true];
        $response = Yii::$app->getResponse();
        $response->format = $response::FORMAT_JSON;
        if (!$model->load($request->post()) || !$model->validate()) {
            $return['status'] = false;
            $return['msg'] = implode(',', array_values($model->errors)[0]);
        } elseif (!$model->validatePassword($model->oldPassword)) {
            $return['status'] = false;
            $return['msg'] = '旧密码错误';
        } elseif (!$model->resetPassword()) {
            $response->setStatusCode(500);
            $return['status'] = false;
            $return['msg'] = '系统异常';
        }
        LogService::saveOperationLog(OperationLog::UPDATE, __METHOD__, $model->id . '修改密码');
        return $return;
    }
}

