<?php
namespace app\controllers;

use common\redis\Member;
/**
 * 
 * @author Yong
 *
 */
class UserController extends BaseController
{
    public function verbs()
    {
        return [
            'test' => ['GET'],
            'login' => ['POST'],
            'update' => ['POST'],
            'get-info' => ['GET'],
            'register' => ['POST'],
        ];
    }
    
    /**
     * 注册
     */
    public function actionRegister()
    {
        return $this->service->register();
    }
    
    /**
     * 登录
     */
    public function actionLogin()
    {
        return $this->service->login();
    }
    
    /**
     * 信息获取
     */
    public function actionGetInfo()
    {
        return $this->service->getInfo();
    }
    
    /**
     * 信息修改
     */
    public function actionUpdate()
    {
        return $this->service->update();
    }
    
    public function actionTest()
    {
        return Member::find()->all();
        $this->service->model=\Yii::$app->getUser()->getIdentity();
        return [$this->vaildate, $this->service];
    }
    
    
}